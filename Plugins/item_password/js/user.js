$(document).ready(function() {
  // Open reservation form
  $('body').on('click', '.itp-open-box', function(e) {
    e.preventDefault();

    var type = $(this).attr('data-type');
    var html = $(this).siblings('.itp-box-content').html();
    
    if(type == 'edit') {
      var height = 285;
    } else {
      var height = 365;
    }     

    itpModal({
      width: 360,
      height: height,
      fromUrl: false,
      content: html, 
      wrapClass: 'itp-mod-form',
      closeBtn: true, 
      iframe: false, 
      fullscreen: false,
      transition: 200,
      delay: 0,
      lockScroll: true
    });
  });
  
  
  // Convert confirmation word "DELETE" to uppercase
  $('body').on('change keyup', 'form[name="itp-delete"] input[name="sConfirm"]', function(){  
    $(this).val($(this).val().toUpperCase());  
  });
});


// CUSTOM MODAL BOX
function itpModal(opt) {
  width = (typeof opt.width !== 'undefined' ? opt.width : 480);
  height = (typeof opt.height !== 'undefined' ? opt.height : 480);
  content = (typeof opt.content !== 'undefined' ? opt.content : '');
  wrapClass = (typeof opt.wrapClass !== 'undefined' ? ' ' + opt.wrapClass : '');
  closeBtn = (typeof opt.closeBtn !== 'undefined' ? opt.closeBtn : true);
  iframe = (typeof opt.iframe !== 'undefined' ? opt.iframe : true); 
  fromUrl = (typeof opt.fromUrl !== 'undefined' ? opt.fromUrl : false); 
  fullscreen = (typeof opt.fullscreen !== 'undefined' ? opt.fullscreen : false); 
  transition = (typeof opt.transition !== 'undefined' ? opt.transition : 200); 
  delay = (typeof opt.delay !== 'undefined' ? opt.delay : 0);
  lockScroll = (typeof opt.lockScroll !== 'undefined' ? opt.lockScroll : true); 

  var id = Math.floor(Math.random() * 100) + 10;
  width = itpAdjustModalSize(width, 'width') + 'px';
  height = itpAdjustModalSize(height, 'height') + 'px';

  var fullscreenClass = '';
  if(fullscreen === 'mobile') {
    if (($(window).width() + scrollCompensate()) < 768) {
      width = 'auto'; height = 'auto'; fullscreenClass = ' modal-fullscreen';
    }
  } else if (fullscreen === true) {
    width = 'auto'; height = 'auto'; fullscreenClass = ' modal-fullscreen';
  }

  var html = '';
  html += '<div class="modal-cover" data-modal-id="' + id + '" onclick="itpModalClose(\'' + id + '\');"></div>';
  html += '<div id="itpModal" class="modal-box' + wrapClass + fullscreenClass + '" style="width:' + width + ';height:' + height + ';" data-modal-id="' + id + '">';
  html += '<div class="modal-inside">';
  
  if(closeBtn) {
    html += '<div class="modal-close" onclick="itpModalClose(\'' + id + '\');">';
    html += '<svg viewBox="0 0 352 512" width="18" height="18"><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg>';
    html += '</div>';
  }
    
  html += '<div class="modal-body ' + (iframe === true ? 'modal-is-iframe' : 'modal-is-inline') + '">';
  
  if(iframe === true) {
    html += '<div class="modal-content"><iframe class="modal-iframe" data-modal-id="' + id + '" src="' + content + '"/></div>';
  } else if(fromUrl === true) {
    html += '<div class="modal-content"><div class="modal-loader"></div></div>';
  } else {
    html += '<div class="modal-content">' + content + '</div>';
  }
  
  html += '</div>';
  html += '</div>';
  html += '</div>';
  
  if(lockScroll) {
    $('body').css('overflow', 'hidden');
  }
  
  $('body').append(html);
  $('div[data-modal-id="' + id + '"].modal-cover').fadeIn(transition);
  $('div[data-modal-id="' + id + '"].modal-box').delay(delay).fadeIn(transition);
  
  if(fromUrl === true) {
    $.ajax({
      url: content,
      type: "GET",
      success: function(response){
        $('.modal-box[data-modal-id="' + id + '"] .modal-content').html(response);
      },
      error: function(response){
        $('.modal-box[data-modal-id="' + id + '"] .modal-content').html(response);
      }
    });
  }
}


// Close modal by clicking on close button
function itpModalClose(id = '', elem = null) {
  if(id == '') {
    id = $(elem).closest('.modal-box').attr('data-modal-id');
  }
  
  $('body').css('overflow', 'initial');
  $('div[data-modal-id="' + id + '"]').fadeOut(200, function(e) {
    $(this).remove(); 
  });
  
  return false;
}


// Close modal by some action inside iframe
function itpModalCloseParent() {
  var boxId = $(window.frameElement, window.parent.document).attr('data-modal-id');
  window.parent.itpModalClose(boxId);
}


// Calculate maximum width/height of modal in case original width/height is larger than window width/height
function itpAdjustModalSize(size, type = 'width') {
  var size = parseInt(size);
  var windowSize = (type == 'width' ? $(window).width() : $(window).height());
  
  if(size <= 0) {
    size = (type == 'width' ? 640 : 480);  
  }
  
  if(size*0.9 > windowSize) {
    size = windowSize*0.9;
  }
  
  return Math.floor(size);
}