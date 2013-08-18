function segmentsOverlap(seg1, seg2) {
  if((seg2.start < seg1.end) && (seg2.end > seg1.start)) {
    return {
      start: Math.max(seg1.start, seg2.start),
      end: Math.min(seg1.end, seg2.end)
      };
  }
  return false;
}

function getHorizontalFrameSegment(frame) {
  return {
    start: frame.origin.x,
    end: frame.origin.x + frame.size.width
  };
}
function getVerticalFrameSegment(frame) {
  return {
    start: frame.origin.y,
    end: frame.origin.y + frame.size.height
  };
}

function framesShareHorizontalSpace(frame1, frame2) {
  var frame1HorizontalSegment = getHorizontalFrameSegment(frame1);
  var frame2HorizontalSegment = getHorizontalFrameSegment(frame2);
  return segmentsOverlap(frame1HorizontalSegment, frame2HorizontalSegment);
}
function framesShareVerticalSpace(frame1, frame2) {
  var frame1VerticalSegment = getVerticalFrameSegment(frame1);
  var frame2VerticalSegment = getVerticalFrameSegment(frame2);
  return segmentsOverlap(frame1VerticalSegment, frame2VerticalSegment);  
}
function framesIntersection(frame1, frame2) {
  var horiz = framesShareHorizontalSpace(frame1, frame2);
  var vert = framesShareVerticalSpace(frame1, frame2);
  if(!(horiz && vert)) {
    return false;
  }
  var x = horiz.start;
  var y = vert.start;
  var width = horiz.end - horiz.start;
  var height = vert.end - vert.start;
  return {
    origin: {
      x:x,
      y:y
    },
    size: {
      width:width,
      height:height
    }
  };
}

function framesIdentical(frame1, frame2) {
  return frame1.origin.x == frame2.origin.x
    && frame1.origin.y == frame2.origin.y
    && frame1.size.width == frame2.size.width
    && frame1.size.height == frame2.size.height;
}

(function( $ ) {
  $.fn.frameInWindow = function() {
    if(window == this[0]) {
      return {
        origin: {x:window.scrollX,y:window.scrollY},
        size: {width:window.innerWidth, height:window.innerHeight}
      };
    }
    return {
      origin : {
        x: this.offset().left,
        y: this.offset().top        
      },
      size : {
        width: this.width(),
        height: this.height()        
      }
    };    
  };
  $.fn.styleMatch = function(prop, value) {
    var matches = [];
    this.each(function(){
      if(-1 != this.style[prop].indexOf(value)) matches.push(this);
      if(-1 != $(this).css(prop).indexOf(value)) matches.push(this);
    });
    return $(matches);
  };
  $.fn.frameIntersection = function(containerElement) {
    return framesIntersection(this.frameInWindow(), containerElement.frameInWindow());
  };

  $.expr.filters.actuallyVisible = function(el) {
    el = $(el);
    var clippingParents = el.parents().styleMatch('overflow', 'hidden');
    var obscuredByClipping = false;
    clippingParents.each(function() {
      if(!el.frameIntersection($(this))) {
        console.log("OBSURED BY CLIPPING!!");
        obscuredByClipping = true;
      }
    })
    return (
      el.is(':visible')
      && el.frameIntersection($(window))
      && !obscuredByClipping
    );
  };

  $.expr.filters.partiallyClipped = function(el) {
    el = $(el);
    if(!el.is(':actuallyVisible')) {
      return false;
    }
    var clipping = false;
    var myFrame = el.frameInWindow();
    var clippingCandidates = el.parents().styleMatch('overflow', 'hidden');
    clippingCandidates.push($(window));
    clippingCandidates.each(function() {
      var sharedFrame = el.frameIntersection($(this));
      if(sharedFrame && !framesIdentical(sharedFrame, myFrame)) {
        clipping = true;
      }
    });
    if(clipping) return clipping;
    
    clippingCandidates = $('*');
    myZ = el.css('z-index');
    if(myZ == 'auto') myZ = 0;
    clippingCandidates.each(function() {
      var $this = $(this);
      var sharedFrame = el.frameIntersection($this);
      var candidateZ = $this.css('z-index');
      if(candidateZ == 'auto') candidateZ = 0;
      if(sharedFrame && (candidateZ > myZ)) {
        clipping = true;
      }
    });
    return clipping;
  };

}( jQuery ));
