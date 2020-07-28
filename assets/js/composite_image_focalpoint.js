class ImageFocalPoint {
  focalPoint = null;
  imageID = null;
  imageElement = null;
  loader = null;
  input = null;
  toggle = null;
  save = null;
  success = null;
  error = null;

  constructor(container, imageID, imageElement) {
    this.imageID = imageID;
    this.imageElement = imageElement;
    container.append(this.createActions());
    this.toggle.addEventListener('click', () => {
      if (this.focalPoint) {
        this.focalPoint.destroy();
        this.focalPoint = null;
      } else {
        this.focalPoint = new FocalPoint(this.imageElement, this.input);
        this.focalPoint.displayCrosshair();
      }
    });
    this.save.addEventListener('click', () => this.saveEvent());
    this.initialize();
    container.classList.add('focalpoint-initialized');
  }

  createActions() {
    const inputContainer = document.createElement('div');
    inputContainer.setAttribute('class', 'image-focalpoint-actions');
    this.createInput();
    inputContainer.append(this.input);
    this.createToggle();
    inputContainer.appendChild(this.toggle);
    this.createSave();
    inputContainer.appendChild(this.save);
    this.createLoader();
    inputContainer.append(this.loader);
    this.createSuccess();
    inputContainer.append(this.success);
    this.createError();
    inputContainer.append(this.error);
    return inputContainer;
  }

  initialize() {
    const request = new XMLHttpRequest();
    request.open('GET', `/wp-json/content-hub-editor/focalpoint/${this.imageID}`, true);
    request.onload = () => {
      if (request.status === 200) {
        const response = JSON.parse(request.response);
        if (response && response.focalpoint) {
          this.enable(response.focalpoint);
        }
      }
    };
    request.onerror = () => console.error(event);
    request.send();
  }

  createInput()
  {
    this.input = document.createElement('input');
    this.input.setAttribute('class', 'text');
    this.input.setAttribute('type', 'text');
    this.input.setAttribute('disabled', 'disabled');
  }

  createToggle() {
    this.toggle = document.createElement('button');
    this.toggle.setAttribute('class', 'button');
    this.toggle.setAttribute('type', 'button');
    this.toggle.setAttribute('disabled', 'disabled');
    this.toggle.innerText = 'Toggle Focal Point Crosshair';
  }

  createSave() {
    this.save = document.createElement('button');
    this.save.setAttribute('class', 'button button-primary');
    this.save.setAttribute('type', 'button');
    this.save.setAttribute('disabled', 'disabled');
    this.save.innerText = 'Save Focalpoint';
  }

  createLoader() {
    this.loader = document.createElement('span');
    this.loader.setAttribute('class', 'spinner is-active');
  }

  createSuccess() {
    this.success = document.createElement('span');
    this.success.setAttribute('class', 'dashicons dashicons-yes');
    this.success.style.display = 'none';
  }

  createError() {
    this.error = document.createElement('span');
    this.error.setAttribute('class', 'dashicons dashicons-no');
    this.error.style.display = 'none';
  }

  enable(focalPoint) {
    this.input.value = `${focalPoint.x},${focalPoint.y}`;
    this.loading(false);
  }

  loading(isLoading) {
    if (isLoading) {
      this.loader.style.display = 'inline-block';
      this.toggle.setAttribute('disabled', 'disabled');
      this.toggle.removeEventListener('click', this.toggleEvent);
      this.save.setAttribute('disabled', 'disabled');
    } else {
      this.loader.style.display = 'none';
      this.toggle.removeAttribute('disabled');
      this.save.removeAttribute('disabled');
    }
  }

  saveEvent() {
    this.loading(true);
    const coords = this.input.value.split(',');
    if (!coords[0] || !coords[1]) {
      this.saveError();
      return;

    }
    const request = new XMLHttpRequest();
    request.open('POST', `/wp-json/content-hub-editor/focalpoint/${this.imageID}`, true);
    request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
    request.onload = () => {
      if (request.status === 200 && request.status < 400) {
        this.loading(false);
        this.success.style.display = 'inline-block';
        setTimeout(() => {
          this.success.style.display = 'none';
        }, 2000);
      } else {
        this.saveError();
      }
    };
    request.onerror = () => {
      this.saveError();
    };
    request.send(JSON.stringify({
      x: coords[0],
      y: coords[1],
    }));
  }

  saveError() {
    this.loading(false);
    this.error.style.display = 'inline-block';
    setTimeout(() => {
      this.error.style.display = 'none';
    }, 2000);
  }
}

(function () {
  let emptyImageFields = [];
  let timeout = null;
  acf.add_action('append', function () {
    addFocalPoints();
  });

  acf.add_action('ready', function () {
    addFocalPoints();
  });

  function addFocalPoints() {
    const imageContainers = document.querySelectorAll('[data-name="composite_content"] .values .acf-field-image .acf-image-uploader, [data-name="teaser_image"] .acf-input .acf-image-uploader');
    enableFocalPoints(imageContainers);
  }

  function enableFocalPoints(imageContainers) {
    if (imageContainers.length) {
      imageContainers.forEach((container, containerIndex) => {
        if (container.classList.contains('focalpoint-initialized')) {
          return
        }
        const index = emptyImageFields.indexOf(container);
        const input = container.querySelector('input');
        const imageID = input.value;
        const imageElement = container.querySelector('img');
        if (!imageID) {
          if (index < 0 ) {
            emptyImageFields.push(container);
          }
        } else {
          // Stagger the creation of ImageFocalPoint to minimize
          // simultaneous requests to the backend.
          setTimeout(() => {
            createImageFocalPoint(container, imageID, imageElement)
          }, containerIndex * 300)
          if (index >= 0) {
            emptyImageFields.splice(index, 1);
          }
        }
      });
      if (emptyImageFields.length && !timeout) {
        timeout = setTimeout(() => {
          timeout = null;
          enableFocalPoints(emptyImageFields)
        }, 3000);
      }
    }
  }

  function createImageFocalPoint(container, imageID, imageElement) {
    new ImageFocalPoint(container, imageID, imageElement);
  }

})();
