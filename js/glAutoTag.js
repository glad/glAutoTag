/*
	glAutoTag - A simple, customizable, lightweight auto-complete and tags plugin for jQuery

	Downloads, examples, and instructions available at:
	http://code.gautamlad.com/glAutoTag/

	Complete project source available at:
	https://github.com/glad/glAutoTag/

	Copyright (c) 2012 Gautam Lad.  All rights reserved.

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.

	Changelog:
		Version 1.0 - Web Feb 1 2011
			- Initial release
*/
(function($)
{
	var defaults =
	{
		cssName:"default",
		delimiter:" ",
		keyDelay:10,
		onChange:null,
		onLookup:null
	};

	var KEYS = { DOWN:40, UP:38, TAB:9 };

	var methods =
	{
		keyTimer: 0,

		init: function(options)
		{
			return this.each(function()
			{
				var self = $(this);
				var settings = $.extend({}, defaults);

				if(options){ settings = $.extend(settings, options); }

				self.data("settings", settings);

				// Bind events for handling list
				self
					.keyup(methods.keyup)
					.keydown(methods.keydown)
					.keypress(methods.keypress);

				// Create our wrappers
				methods.create.apply($(this));

				// Bind click elsewhere to hide
				$(window).bind("click", function(e)
				{
					methods.hide.apply(self);
				});
			});
		},
		
		// Create wrapper elements
		create: function()
		{
			var input = $(this);
			var settings =input.data("settings");
			var css = settings.cssName;
			var id = input[0].id;

			// Box container containing input field
			settings.box = 
			$(
				"<div class='_glat-box glat-"+css+"-box'>"+
					"<div class='_glat-input-box glat-"+css+"-input-box'></div>"+
					"<div class='_glat-result-box glat-"+css+"-result-box'><ul class='_glat-results'></ul></div>"+
					"<div class='_glat-tags-box glat-"+css+"-tags-box'><ul class='_glat-tags'></ul></div>"+
				"</div>"
			);
			
			// Get pointers to elements within box
			settings.tList = settings.box.find("ul._glat-tags");
			settings.rList = settings.box.find("ul._glat-results");

			// Add box container after input field
			input.after(settings.box);

			// Move input field into the input box container
			input.appendTo(settings.box.find("div._glat-input-box"));
		},

		// Show the results box
		show: function()
		{
			var input = $(this);
			var settings = input.data("settings");

			// Instead of catching blur we'll find anything that's made visible
			methods.hide.apply($(this));

			// Reposition result box
			settings.rList
				.parent().show()
				.offset(
				{
					left:input.offset().left,
					top:input.offset().top+input.outerHeight(true)
				})
				.width(input.outerWidth()-2); // -2 to account for border
		},
		
		// Hide the results box
		hide: function()
		{
			$(this).data("settings").rList.parent().hide();
		},

		// Get caret position
		// Based on http://stackoverflow.com/questions/2897155/get-caret-position-within-an-text-input-field/2897510#2897510
		caret: function()
		{
			var input = $(this)[0];

			// IE Support
			if(document.selection)
			{
				input.focus();
		
				var sel = document.selection.createRange();
				var selLen = document.selection.createRange().text.length;

				sel.moveStart('character', -input.value.length);

				return sel.text.length - selLen;
			}
			// Firefox support
			else if(input.selectionStart || input.selectionStart == '0')
			{
				return input.selectionStart;
			}

			return 0;
		},

		// Update tags
		update: function()
		{
			var input = $(this);
			var settings = input.data("settings");

			// Remove tags
			settings.tList.children("li").remove();

			// Read tags and add list
			$.each(settings.tags, function(i, o)
			{
				settings.tList.append($("<li/>").html(o));
			});

			// Run callback to user-defined date change method
			if(settings.onChange != null && typeof settings.onChange != "undefined")
			{
				settings.onChange(input, settings.tags);
			}
		},

		keypress: function(e)
		{
			e.stopPropagation();

			$(this).data("settings").lastKeyCode = e.keyCode;
		},

		keydown: function(e)
		{
			var input = $(this);
			var settings = input.data("settings");

			e.stopPropagation();

			if(e.keyCode == KEYS.TAB)
			{
				// Prevent tab from being applied
				e.preventDefault();

				// Get the current active selection
				var a = settings.rList.children("li.active");

				// Make sure we have an item
				if(a.length > 0)
				{
					// Build up results
					var result = "";
					$.each(settings.tags, function(i, o)
					{
						if(o == settings.lastTag)
						{
							o = settings.tags[i] = a.data("value");
						}

						result += o + settings.delimiter;
					});

					// Replace text in input with proper tags
					input.val(result);

					// Hide result list
					methods.hide.apply($(this));
				}

				// Update tag list
				methods.update.apply(input);
			}

			else if(e.keyCode == KEYS.DOWN || e.keyCode == KEYS.UP)
			{
				e.preventDefault();

				var a = settings.rList.children("li.active");
				var li = (e.keyCode == KEYS.UP)? a.prev("li"): a.next("li");

				if(li.length == 0 && e.keyCode == KEYS.UP)
				{
					li = settings.rList.children("li:last");
				}
				else if(li.length == 0 && e.keyCode == KEYS.DOWN)
				{
					li = settings.rList.children("li:first");
				}

				settings.rList.children("li").removeClass("active");
				li.addClass("active");
			}
		},

		keyup: function(e)
		{
			var input = $(this);
			var settings = input.data("settings");

			e.stopPropagation();

			clearTimeout(methods.keyTimer);
			methods.keyTimer = setTimeout(function()
			{
				var value = input.val();
				var delimiter = settings.delimiter;
				var words = value.split(delimiter);

				if(!(e.keyCode == KEYS.TAB ||e.keyCode == KEYS.DOWN || e.keyCode == KEYS.UP))// || (String.fromCharCode(settings.lastKeyCode) == delimiter)))
				{
					// Remove all LI
					settings.tList.children("li").remove();

					// Reset last tag and list of tags
					settings.lastTag = "";
					settings.tags = new Array();

					// Extract tags
					$.each(words, function(i, o)
					{
						if(typeof o != "undefined")
						{
							o = o.replace(/^\s+|\s+$/gi,"");
							if(o != "") { settings.tags.push(o); }
						}
					});

					// Get caret pos
					var cpos = methods.caret.apply(input);

					// Determine word at caret
					var spos = 0;
					var epos = value.length - 1;
					$.each(value, function(i, o)
					{
						// We're at our caret pos.  Find the word that we're at
						if(i <= cpos)
						{
							// Init the start/end positions
							spos = epos = i;

							// Get first character position
							while(spos >= 0) { if(value[--spos] == delimiter) { break; } } spos++;

							// Get last character position
							while(epos < value.length) { if(value[epos++] == delimiter) { break; } }

							// Get the tag based on caret position
							settings.lastTag = value.substring(spos, epos).replace(/\s+/gi,"").replace(delimiter,"");
						}
					});

					// Do a lookup based on last tag
					if(settings.lastTag != "" && settings.lastTag != delimiter)
					{
						// Run callback to user-defined lookup
						if(settings.onLookup != null && typeof settings.onLookup != "undefined")
						{
							// Show the results box
							methods.show.apply(input);

							// Trigger use-defined lookup callback
							settings.onLookup(settings.lastTag, settings.rList);
						}
					}
					else
					{
						// Hide list on empty last tag or no post url
						settings.rList.parent().hide();
					}

					// Update tag list
					methods.update.apply(input);
				}
			}, settings.keyDelay);
		}
	};

	// Plugin entry
	$.fn.glAutoTag = function(method)
	{
		if(methods[method]){ return methods[method].apply(this, Array.prototype.slice.call(arguments, 1)); }
		else if(typeof method === "object" || !method){ return methods.init.apply(this, arguments); }
		else { $.error("Method "+ method + " does not exist on jQuery.glAutoTag"); }
	};
})(jQuery);