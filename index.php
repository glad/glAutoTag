<?php header('Cache-Control: no-cache, must-revalidate'); header('Content-type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta name="description" content="glAutoTag - A simple, customizable, lightweight auto-tag calendar plugin for jQuery" />
	<meta name="keywords" content="datepicker, auto-tag, calendar, date control, jQuery" />
	<meta name="author" content="Gautam Lad" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>glAutoTag - A simple, customizable, lightweight auto-complete and tags plugin for jQuery</title>

	<link href="site/css/screen.css" rel="stylesheet" type="text/css" />
	<link href="site/css/syntaxhighlighter.css" rel="stylesheet" type="text/css" />

	<link href="css/default.css" rel="stylesheet" type="text/css" />
	<link href="css/stackoverflow.css" rel="stylesheet" type="text/css" />
</head>

<body>
	<div id="#" class="site">
		<div class="container">

			<!-- BEGIN header -->
			<div class="header">
				<div class="title">glAutoTag</div>
				<div class="menu">
					<ul>
						<li><a href="#download">download</a></li>
						<li><a href="#usage">usage</a></li>
						<li><a href="#examples">examples</a></li>
						<li><a href="#about">about</a></li>
					</ul>
				</div>
			</div>
			<!-- END header -->

			<!-- BEGIN about -->
			<div class="content">
				<div id="about" class="title">about</div>
				<p>
					<b>glAutoTag</b> is a simple, customizable, lightweight auto-complete and tags plugin for <a href="http://jquery.com" target="_blank">jQuery</a> weighing in just over <span class="special">3KB compressed</span> (9KB uncompressed).
					<br/>
					<img src="site/img/screenshot.png" width="575" height="335" alt="Example styles" />
					<br/>
					It includes the following features:
				</p>
				<ul class="features">
					<li>use any character as delimiter</li>
					<li>custom callback for performing lookup</li>
					<li>individual styles per control (in case you have multiples on one page)</li>
				</ul>
			</div>
			<!-- END about -->

			<!-- BEGIN examples -->
			<div class="content">
				<div id="examples" class="title">examples</div>
				<p>
					Click on the text boxes to see examples of the control with the settings shown.
				</p>

				<!-- BEGIN Example #1 -->
				<div class="example-box">
					<p>
						<b><span class="example">Example #1</span>: Basic usage.  No lookup.</b>
					</p>
					<input type="text" id="input1" class="glat" />
					<pre class="brush:js">
						// Basic auto-tag with default settings
						$("#input1").glAutoTag();</pre>
				</div>
				<!-- END Example #1 -->

				<!-- BEGIN Example #2 -->
				<div class="example-box">
					<p>
						<b><span class="example">Example #2</span>: Use a custom styled (only for this control) with lookup</b>
					</p>
					<input type="text" id="input2" class="glat" />
					<pre class="brush:js">
						// Use a custom theme named stackoverflow.  Get lookups from the server
						$("#input2").glAutoTag(
						{
							cssName: "stackoverflow",
							onLookup: function(tag, resultList)
							{
								doLookup("stackoverflow", tag, resultList);
							}
						});</pre>
				</div>
				<!-- END Example #2 -->

				<!-- BEGIN Example #3 -->
				<div class="example-box">
					<p>
						<b><span class="example">Example #3</span>: Using semi-colon as a delimiter with lookup</b>
					</p>
					<input type="text" id="input3" class="glat" />
					<pre class="brush:js">
						// Use a custom theme named stackoverflow.  Get lookups from the server
						$("#input3").glAutoTag(
						{
							delimiter: ";",
							onLookup: function(tag, resultList)
							{
								doLookup("default", tag, resultList);
							}
						});</pre>
				</div>
				<!-- END Example #3 -->

				<!-- BEGIN doLookup -->
				<div class="example-box">
					<p>
						<b><span class="example">Lookup method</span>: Used by the above two lookup examples.  Handles the results returned by the lookup and adds them to the result list.</b>
					</p>
					<pre class="brush:js">
						// Perform lookup
						function doLookup(mode, tag, resultList)
						{
							// Make an ajax post call
							$.ajax(
							{
								url: "tags.php?mode=" + mode,
								dataType: "json",
								data: {"input" : tag},
								type: "post",
								success: function(json)
								{
									// Remove all items from results
									resultList.children("li").remove();

									// Make sure we have something
									if(typeof json != "undefined" && json != null)
									{
										// Iterate through our results from the server
										$.each(json, function(i, o)
										{
											// Add a &lt;li&gt; to our result and show the result box
											// IMPORTANT: Make sure to save the raw value in data()
											var li = $("&lt;li/&gt;").html(o.display).data("value", o.value)

											resultList.append(li).parent().show();

											console.log(li.outerHeight(true));
										});

										// Hide the list if nothing available
										if(resultList.children("li").length == 0)
										{
											resultList.parent().hide();
										}
										else
										{
											// Set first item to selected
											resultList.children("li:first").attr("class", "active");
										}
									}
								}
							});
						}</pre>
				</div>
				<!-- END doLookup -->
			</div>
			<!-- END examples -->

			<!-- BEGIN usage -->
			<div class="content">
				<div id="usage" class="title">usage</div>
				<p>
					Below are all the available settings that can be adjusted and public methods that can be called.
					The values shown are the default setting values.
				</p>

				<pre class="brush:js">
					/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
					 * Settings
					 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

					$("#input").glAutoTag(
					{
						// Name of the stylesheet to use.
						// For example, if your css name is called doublerainbow
						// then all all your css elements will need to be
						// prefixed with .glat-doublerainbow in your stylesheet.
						// Use the /css/default.css as a starting point.
						cssName: "default",

						// Delimiter used for separating tags.
						cssName: " ",

						// Delay (in ms) between keystrokes to wait before processing input
						// Possible values: an integer greater than or equal to 0 (ms)
						keyDelay: 100,

						// A callback function called whenever the tag list is updated
						// Possible values: a function with two arguments: target, tags
						// For example:
						// $("#input").glAutoTag(
						// {
						//     onChange function(target, tags)
						//     {
						//     }
						// });
						onChange null,

						// A callback function to call to perform lookup and handle results.
						// Possible values: a function with two arguments: tag, and resultList
						// For example:
						// $("#input").glAutoTag(
						// {
						//     onLookup: function(tag, resultList)
						//     {
						//     }
						// });
						onLookup: null
					});
			</div>
			<!-- END usage -->

			<!-- BEGIN download -->
			<div class="content">
				<div id="download" class="title">download - Coming soon!</div>
				<ul>
					<li><a href="site/download/glAutoTag-v1.0.zip">glAutoTag-v1.0.zip</a> - Released Feb 1, 2012</li>
				</ul>
			</div>
			<!-- END download -->

			<!-- BEGIN download -->
			<div class="content">
				<div id="clone" class="title">clone from github</div>
				Clone the latest source directly from the <a href="https://github.com/glad/glAutoTag/">github</a> repository.
				<pre class="brush:bash">$ git clone https://github.com/glad/glAutoTag.git</pre>
			</div>
			<!-- END download -->

			<!-- BEGIN footer -->
			<div class="footer">
				<span class="copyright">Copyright &#169; 2012 <a href="http://gautamlad.com/">Gautam Lad</a>.  All rights reserved</span>
				<span class="contact"><a href="mailto:email@gautamlad.com">contact</a></span>
			</div>
			<!-- BEGIN footer -->
		</div>
	</div>

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script src="site/js/XRegExp.js" type="text/javascript"></script>
	<script src="site/js/shCore.js" type="text/javascript"></script>
	<script src="site/js/shBrushJScript.js" type="text/javascript"></script>
	<script src="site/js/shBrushBash.js" type="text/javascript"></script>

	<script type="text/javascript" src="js/glAutoTag.js"></script>
	<script type="text/javascript">
		$(document).ready(function()
		{
			SyntaxHighlighter.defaults["brush"] = "js";
			SyntaxHighlighter.defaults["ruler"] = false;
			SyntaxHighlighter.defaults["toolbar"] = false;
			SyntaxHighlighter.defaults["gutter"] = false;
			SyntaxHighlighter.all();

			// Basic auto-tag with default settings
			$("#input1").glAutoTag();

			// Use a custom theme named stackoverflow and get lookup results from the server
			$("#input2").glAutoTag(
			{
				cssName: "stackoverflow",
				onLookup: function(tag, resultList)
				{
					doLookup("stackoverflow", tag, resultList);
				}
			});

			// Use a semi-colon as delimiter and get lookup results from the server
			$("#input3").glAutoTag(
			{
				delimiter: ";",
				onLookup: function(tag, resultList)
				{
					doLookup("default", tag, resultList);
				}
			});

			// Perform lookup
			function doLookup(mode, tag, resultList)
			{
				// Make an ajax post call
				$.ajax(
				{
					url: "tags.php?mode=" + mode,
					dataType: "json",
					data: {"input" : tag},
					type: "post",
					success: function(json)
					{
						// Remove all items from results
						resultList.children("li").remove();

						// Make sure we have something
						if(typeof json != "undefined" && json != null)
						{
							// Iterate through our results from the server
							$.each(json, function(i, o)
							{
								// Add a <li> to our result and show the result box
								// IMPORTANT: Make sure to save the raw value in data()
								var li = $("<li/>").html(o.display).data("value", o.value)

								// Add item to list and show
								resultList.append(li).parent().show();

								// Adjust list box based on # of items
								resultList.parent().height(li.outerHeight(true) * 5);
							});

							// Hide the list if nothing available
							if(resultList.children("li").length == 0)
							{
								resultList.parent().hide();
							}
							else
							{
								// Set first item to selected
								resultList.children("li:first").attr("class", "active");
							}
						}
					}
				});
			}
		});
	</script>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-7701484-3']);
		_gaq.push(['_setDomainName', '.gautamlad.com']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
</body>
</html>