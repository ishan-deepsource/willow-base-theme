class FocalPoint {
  image = null;
  imageW = null;
  imageH = null;
  imgPos = null;
  crosshair = null;
  input = null;
  showCrosshair = false;
  imageClickEvent = null;

  constructor(image, input) {
    if (window.focalPoint) {
      window.focalPoint.destroy();
      window.focalPoint = null;
    }
    window.focalPoint = this;

    if (!image) {
      console.error('instantiated without image reference');
    }
    if (!input) {
      console.error('instantiated without input reference');
    }

    this.image = image;
    this.bootstrapImage();
    this.input = input;
    this.input.setAttribute('readonly', '');
    if(!this.input.value) {
      this.input.value = '0.5,0.5';
    }
  }

  addToggleButton()
  {
    const button = document.createElement('button');
    button.setAttribute('class', 'button');
    button.setAttribute('type', 'button');
    button.innerText = 'Toggle Focal Point Crosshair';
    button.addEventListener('click', () => {
      if (!this.showCrosshair) {
        this.displayCrosshair();
      } else {
        this.hideCrosshair();
      }
    });
    this.input.parentNode.appendChild(button);
  }

  bootstrapImage() {
    this.imgPos = this.findPosition(this.image);
    this.imageW = this.image.width;
    this.imageH = this.image.height;
    this.imageClickEvent = (e) => this.registerFocalPoint(e);
    this.image.addEventListener('click', this.imageClickEvent);
    this.createCrosshair();
    this.image.parentNode.appendChild(this.crosshair);
    this.image.parentNode.style.position = 'relative';
  }

  findPosition(oElement) {
    if(typeof( oElement.offsetParent ) != "undefined")
    {
      for(var posX = 0, posY = 0; oElement; oElement = oElement.offsetParent)
      {
        posX += oElement.offsetLeft;
        posY += oElement.offsetTop;
      }
      return {x: posX, y: posY};
    }
    else
    {
      return {x: oElement.x, y: oElement.y };
    }
  }

  registerFocalPoint(e) {
    if(!this.showCrosshair) {
      return;
    }
    var posX = 0;
    var posY = 0;
    if (e.pageX || e.pageY) {
      posX = e.pageX;
      posY = e.pageY;
    } else if (e.clientX || e.clientY) {
      posX = e.clientX + document.body.scrollLeft
        + document.documentElement.scrollLeft;
      posY = e.clientY + document.body.scrollTop
        + document.documentElement.scrollTop;
    }

    posX = posX - this.imgPos.x;
    posY = posY - this.imgPos.y;

    var relX = Math.round((posX / this.imageW) * 100) / 100;
    var relY = Math.round((posY / this.imageH) * 100) / 100;

    this.input.value = relX + ',' + relY;

    this.displayCrosshair();
  }

  displayCrosshair() {
    var relPos = this.input.value.split(',');
    var posX = relPos[0] * this.imageW;
    var posY = relPos[1] * this.imageH;

    this.crosshair.style.left = (posX - 64) + 'px';
    this.crosshair.style.top = (posY - 64) + 'px';
    this.crosshair.style.display = 'block';
    this.showCrosshair = true;
  }

  hideCrosshair() {
    this.crosshair.style.display = 'none';
    this.showCrosshair = false;
  }

  createCrosshair() {
    this.crosshair = document.createElement('img');
    this.crosshair.setAttribute('src', assets.crosshair);
    this.crosshair.style.position = 'absolute';
    this.crosshair.style.display = 'none';
    this.crosshair.style.pointerEvents = 'none';
    this.crosshair.style.backgroundColor = 'transparent';
  }

  destroy() {
    if (this.crosshair) {
      this.crosshair.parentNode.removeChild(this.crosshair);
      this.crosshair = null;
    }
    if (this.image) {
      this.image.removeEventListener('click', this.imageClickEvent);
      this.imageClickEvent = null;
      this.image = null;
    }
    this.input = null;
  }
}
