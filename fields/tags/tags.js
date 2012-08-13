(function($) {

  $.autocomplete = function(element, url, options) {

    var defaults = {
      url : false,
      apply : function(string) { $element.val(string) }
    }

    var plugin = this;

    plugin.settings = {}

    var $element = $(element),
         element = element;

    plugin.init = function() {

      plugin.settings = $.extend({}, defaults, options);

      plugin.input  = $element;
      plugin.ignore = [];
      plugin.data   = {};     
      plugin.open   = false;

      plugin.load();
      
      plugin.blocker = $('<div class="autocomplete-blocker"></div>').css({'position' : 'fixed', 'top' : 0, 'left' : 0, 'right' : 0, 'bottom' : 0, 'z-index' : 1999}).hide();
      plugin.box     = $('<div class="autocomplete"></div>').css('z-index', 2000);
      plugin.ul      = $('<ul></ul>');

      plugin.blocker.click(function(e) {
        plugin.kill();
        plugin.input.focus();
        e.stopPropagation();
      });
    
      $element.keyup(function(e) {
                
        plugin.pos = plugin.selection();        
                
        switch(e.keyCode) {
          case 13: // enter
            plugin.apply();
          case 27: // esc
            plugin.kill();
          case 38: // up
          case 40: // down
            return false;
          case 39: // right
            var value = plugin.value();
            if(value.length > 0 && plugin.pos == plugin.input.val().length) plugin.apply();
            break;
          case 188: // ,
            plugin.apply();
          default: 
            plugin.complete($(this).val());
            break;
        }

      });
      
      $element.keydown(function(e) {

        plugin.pos = plugin.selection();        

        switch(e.keyCode) {
          case 9:  // tab
            if(plugin.value().length > 0) {
              plugin.apply(); 
              return false;
            } else { 
              return true;
            }
            break;
          case 38: // up
            plugin.prev();
            return false;
          case 40: // down
            plugin.next();
            return false;
        }     
      });

      plugin.box.click(function(e) {
        e.stopPropagation();
      });
            
      $('body').append(plugin.box);
      $('body').append(plugin.blocker);
    
      $(window).resize(plugin.position);
      plugin.position();

    }
  
    plugin.load = function() {

      if(typeof url == 'object') {
        plugin.data = url;      
      } else {
        $.getJSON(url, function(result) {
          plugin.data = result;
        });
      }     

    }

    plugin.complete = function(search) {
    
      plugin.kill();
      plugin.position();

      var counter = 0;
      var search  = $.trim(search); 

      if(search.length == 0) return false;

      var reg = new RegExp('^' + search, 'i');

      var result = plugin.data.filter(function(str) {
        if(plugin.ignore.indexOf(str) == -1 && str.match(reg)) return str;
      });
      
      result = result.slice(0,5);
                                  
      $.each(result, function(i, string) {
                                          
        var li = $('<li>' + string + '</li>');
        
        li.click(function() {
          plugin.apply(string)
        });
        
        li.mouseover(function() {
          plugin.ul.find('.selected').removeClass('selected');
          li.addClass('over');          
        });

        li.mouseout(function() {
          li.removeClass('over');
        });
        
        plugin.ul.append(li);

        if(counter==0) li.addClass('first selected');
        counter++;
                      
      });
      
      if(counter > 0) {
        plugin.box.append(plugin.ul.show());        
        plugin.blocker.show();
        plugin.open = true;
      }     
    }
        
    plugin.kill = function() {
      plugin.blocker.hide();
      plugin.ul.empty().hide();
      plugin.open = false;
    }
    
    plugin.apply = function(string) {
      if(!string) {
        var string = plugin.value();
      } 
      plugin.settings.apply.call(plugin, string);
    }
    
    plugin.value = function() {
      return plugin.selected().text();
    }
    
    plugin.selected = function() {
      return plugin.ul.find('.selected');
    }
    
    plugin.select = function(element) {
      plugin.deselect();      
      element.addClass('selected');
    }
    
    plugin.deselect = function() {
      plugin.selected().removeClass('selected');
    }
    
    plugin.prev = function() {
      var sel  = plugin.selected();
      var prev = sel.prev();
      if(prev.length > 0) plugin.select(prev);
    }
    
    plugin.next = function() {
      var sel  = plugin.selected();   
      var next = (sel.length > 0) ? sel.next() : plugin.ul.find('li:first-child');
      if(next.length > 0) plugin.select(next);
    }

    plugin.selection = function() {
      var i = plugin.input[0];
      var v = plugin.val;
      if(!i.createTextRange) return i.selectionStart;
      var r = document.selection.createRange().duplicate();
      r.moveEnd('character', v.length);
      if(r.text == '') return v.length;
      return v.lastIndexOf(r.text);
    }

    plugin.position = function() {
      
      var pos    = $element.offset();
      var height = $element.innerHeight();

      pos.top = pos.top+height+10;
            
      plugin.box.css(pos);
          
    }
  
    plugin.init();
    
  }

  $.fn.autocomplete = function(url, options) {

    return this.each(function() {
      if(undefined == $(this).data('autocomplete')) {
        var plugin = new $.autocomplete(this, url, options);
        $(this).data('autocomplete', plugin);
      }
    });

  }

})(jQuery);






(function($) {

  $.tagbox = function(element, options) {

    var defaults = {
      url          : false,
      autocomplete : {},
      lowercase    : true,
      classname    : 'tagbox',
      separator    : ', ',
      duplicates   : false,
      minLength    : 1,
      maxLength    : 140,
      keydown      : function() { },
      onAdd        : function() { },
      onRemove     : function() { },
      onDuplicate  : function() { return plugin.input.focus() },
      onInvalid    : function() { return plugin.input.focus() },
      onReady      : function() { }
    }

    var plugin = this;
    var autocomplete = null;

    plugin.settings = {}

    var $element = $(element),
         element = element;

    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, options);

      var $name = $element.attr('name');
      var $id   = $element.attr('id');
      var $val  = $element.val();

      plugin.index   = [];
      plugin.val     = '';
      plugin.focused = false;
      plugin.origin  = $element.addClass('tagboxified').hide();
      plugin.box     = $('<div class="' + plugin.settings.classname + '"><ul><li class="new"><input autocomplete="off" tabindex="0" type="text" /></li></ul></div>');
      plugin.input   = plugin.box.find('input').css('width', 20);
      plugin.bhits   = 0;
      plugin.lhits   = 0;
            
      if(plugin.settings.url) {

        var autocompleteDefaults = {
          apply : function(string) {
            plugin.add(string); 
            this.kill();
            plugin.input.focus();
          }        
        };
        
        // initialize the autocomplete plugins with a default event
        plugin.input.autocomplete(plugin.settings.url, $.extend({}, autocompleteDefaults, plugin.settings.autocomplete));
        
        // store the autocomplete plugin object        
        plugin.autocomplete = plugin.input.data('autocomplete');
        
        // add autocomplete custom events to the tagbox plugin                
        plugin.settings.onAdd = function(tag) {
          plugin.autocomplete.ignore = plugin.serialize();
        }
        plugin.settings.onRemove = function(tag) {
          plugin.autocomplete.ignore = plugin.serialize();
        }
      
      }      

      plugin.origin.before(plugin.box);
    
      plugin.measure = $('<div style="display: inline" />').css({
        'font-size'   : plugin.input.css('font-size'),
        'font-family' : plugin.input.css('font-family'),
        'visibility'  : 'hidden',
        'position'    : 'absolute',
        'top'         : -10000,
        'left'        : -10000
      });

      $('body').append(plugin.measure);
                  
      plugin.box.click(function(e) {
        plugin.focus();
        plugin.input.focus();
        e.stopPropagation();
      });
      plugin.input.keydown(function(e) {
        plugin.val = plugin.input.val();
        plugin.position = plugin.selection();                                               
        plugin.settings.keydown.call(plugin, e, plugin.val);
      });
      plugin.input.keyup(function(e) {
        plugin.val = plugin.input.val();
        plugin.position = plugin.selection();                                               
        plugin.resize(plugin.val);
        if(plugin.val.match(new RegExp(plugin.settings.separator))) plugin.add(plugin.val);
      });
      plugin.input.focus(function(e) {
        plugin.input.focused = true;
        plugin.deselect();      
        plugin.bhits = 0;
        plugin.focus();
      });
      plugin.input.blur(function(e) {
        plugin.input.focused = false;
        plugin.bhits = 0;   
        if(plugin.val.length == 0) plugin.blur();               
      });       

      plugin.settings.onReady.call(this);

      $(document).keydown(function(e) {

        if(!plugin.focused) return true;

        switch(e.keyCode) {
          case 8: //backspace
            if(!plugin.input.focused) {
              plugin.remove();
              return false;
            }
            if(plugin.val.length == 0) {
              plugin.next();
              return false;           
            } else if(plugin.position == 0) {
              if(plugin.bhits > 0) {
                plugin.bhits = 0;
                plugin.next();
                return false;
              }
              plugin.bhits++;
            }
            break;      
          case 37: // left
            if(!plugin.input.focused) return plugin.previous();
            if(plugin.val.length == 0) {
              plugin.next();
              return false;           
            } else if(plugin.position == 0) {
              if(plugin.lhits > 0) {
                plugin.lhits = 0;
                plugin.next();
                return false;
              }
              plugin.lhits++;
            }
            break;
          case 39: // right
            if(!plugin.input.focused) {
              plugin.next();            
              return false;
            }
            break;
          case 9: // tab
            if(plugin.input.focused && plugin.val.length > plugin.settings.minLength) {
              plugin.add(plugin.val);
              return false;
            } else if(plugin.selected().length > 0) {
              plugin.deselect();
              plugin.input.focus();
              return false;
            }
            break;
          case 13: // enter
          case 188: // ,
            if(plugin.input.focused) {
              if(!plugin.settings.autocomplete) plugin.add(plugin.val);
              return false;
            }
            break;
        }
      
      }).click(function(e) {
        if(plugin.val.length > 0) plugin.add(plugin.val);
      });

      if($val.length > 0) plugin.add($val);

    }
            
    plugin.resize = function(value) {
      plugin.measure.text(value);
      plugin.input.css('width', plugin.measure.width() + 20);   
    },

    plugin.focus = function(input) {
      if(plugin.focused) return true;
      
      $('.tagboxified').not(plugin.origin).each(function() {
        if($(this).data('tagbox')) $(this).data('tagbox').blur();  
      });

      plugin.box.addClass('focus');
      plugin.focused = true;
      
      if(input == undefined) var input = true;
      if(input !== false) plugin.input.focus();
    }

    plugin.blur = function() {
      if(!plugin.focused) return true;
      plugin.box.removeClass('focus');
      plugin.focused = false;
      plugin.input.blur();    
      plugin.deselect();
    }
        
    plugin.tag = function(tag) {
      tag = tag.replace(/,/g,'').replace(/;/g,'');
      if(plugin.settings.lowercase) tag = tag.toLowerCase();
      return $.trim(tag);   
    }

    plugin.serialize = function() {
      return plugin.index;
    }

    plugin.string = function() {
      return plugin.serialize().toString();
    }

    plugin.add = function(tag) {

      plugin.input.val('');

      if(!tag && plugin.val.length > 0) {
        return plugin.add(plugin.val);
      } else if(!tag) {
        return true;
      }
            
      if($.isArray(tag) || tag.match(new RegExp(plugin.settings.separator))) {      
        var tags = ($.isArray(tag)) ? tag : tag.split(plugin.settings.separator);
        $.each(tags, function(i,t) {
          plugin.add(t);
        }); 
        return true;
      } 
        
      var tag = plugin.tag(tag);
          
      if(tag.length < plugin.settings.minLength || tag.length > plugin.settings.maxLength) {
        return plugin.settings.onInvalid.call(plugin, tag, length);
      }
      
      if(plugin.settings.duplicates == false) {
        if($.inArray(tag, plugin.index) > -1) {
          return plugin.settings.onDuplicate.call(plugin, tag);
        }
      }
      
      plugin.index.push(tag);
      
      var li = $('<li><span class="tag"></span><span class="delete">&#215;</span></li>').data('tag', tag);
      li.find('.tag').text(tag);
      li.find('.delete').click(function() { plugin.remove(li) });
                
      li.click(function(e) {
        plugin.blur();
        e.stopPropagation();          
        plugin.select(li);
      });
      li.focus(function(e) {
        plugin.select(li)
      });
    
      plugin.input.parent().before(li);
      plugin.input.val('');
      plugin.input.css('width', 20);

      var serialized = plugin.serialize();
      plugin.origin.val(serialized.join(plugin.settings.separator));
      plugin.settings.onAdd.call(plugin, tag, serialized, li);
                      
    }

    plugin.select = function(element) {

      if(typeof element == 'string') {
        var element = plugin.find(element);
        if(!element) return false;
      }

      if(element.length == 0) return false;     
      plugin.input.blur();
      this.deselect();
      element.addClass('selected');
      plugin.focus(false);
    }
      
    plugin.selected = function() {
      return plugin.box.find('.selected');  
    }

    plugin.deselect = function() {
      var selected = plugin.selected();
      selected.removeClass('selected'); 
    }

    plugin.find = function(tag) {
      var element = false;
      plugin.box.find('li').not('.new').each(function() {
        if($(this).data('tag') == tag) element = $(this);
      });     
      return element;
    }

    plugin.remove = function(element) {
      
      plugin.input.val('');
      
      if(typeof element == 'string') {
        var element = plugin.find(element);
        if(!element) return false;
      }
      
      var selected = plugin.selected();
      if(!element && selected.length > 0) var element = selected.first();
      var previous = plugin.previous(true);
      (previous.length == 0) ? plugin.next() : plugin.select(previous);
      var tag = element.find('.tag').text();
      plugin.removeFromIndex(tag);
      element.remove();
      var serialized = plugin.serialize();
      plugin.origin.val(serialized);
      plugin.settings.onRemove.call(plugin, tag, serialized, element);
    }

    plugin.removeFromIndex = function(tag) {
      var i = plugin.index.indexOf(tag);
      plugin.index.splice(i,1);
    }
    
    plugin.selection = function() {
      var i = plugin.input[0];
      var v = plugin.val;
      if(!i.createTextRange) return i.selectionStart;
      var r = document.selection.createRange().duplicate();
      r.moveEnd('character', v.length);
      if(r.text == '') return v.length;
      return v.lastIndexOf(r.text);
    }
    
    plugin.previous = function(ret) {
      var sel  = plugin.selected();
      var prev = (sel.length == 0) ? plugin.box.find('li').not('.new').first() : sel.prev().not('.new');
      return (ret) ? prev : plugin.select(prev);
    }
    
    plugin.next = function(ret) {
      var sel  = plugin.selected();
      var next = (sel.length == 0) ? plugin.box.find('li').not('.new').last() : sel.next();
      return (ret) ? next : (next.hasClass('new')) ? plugin.input.focus() : plugin.select(next);
    }

    plugin.init();
    
  }

  $.fn.tagbox = function(options) {

    return this.each(function() {
      if(undefined == $(this).data('tagbox')) {

        var plugin = new $.tagbox(this, options);
        $(this).data('tagbox', plugin);
        
      }
    });

  }

})(jQuery);