(function() {
	
	tinymce.PluginManager.requireLangPack("typograf");

	tinymce.create("tinymce.plugins.Typograf", {
		
		init : function(ed, url) {
			
			this.editor = ed;
			this.url = url;
			
		},
		
		// COMMAND
		
		execCommand: function(cmd, ui, type) {
			
			var ed = tinyMCE.activeEditor;
			var url = this.url;
			var anchorStart = "{mceTypograf-selection-start}"; // This is pretty unique ;)
			var anchorEnd = "{mceTypograf-selection-end}";
			
			type = type || "lebedev"; // Default is A. Lebedev
			
			switch (cmd) {
				
				// Remember to have the "mce" prefix for commands so they don't intersect with built in ones in the browser.
				case "mceTypograf":
					
					// Highlight the control
					ed.controlManager.setActive("typograf", true);
					
					var sel = !ed.selection.isCollapsed();
					
					// Remember current state of content and selection
					var orig = ed.getContent();
					var mark = ed.selection.getBookmark();
					
					// Handle selection
					if (sel) {
						
						var range = ed.selection.getRng()
						var h1 = "", h2 = "", hx = "", text = "";
						
						// Range object has two parts, we will parse them separately
						// Known issues — script can die here if selection goes through the table, we should handle it somehow
						
						if (range.startContainer.data != range.endContainer.data) {
							
							// Start anchor
							text = range.startContainer.data;
							h1 = text.substr(0, range.startOffset);
							h2 = text.substr(range.startOffset);
							
							text = h1 + anchorStart + h2;
							range.startContainer.data = text; // Replace original content with anchored one
							
							// End anchor
							text = range.endContainer.data;
							h1 = text.substr(0, range.endOffset);
							h2 = text.substr(range.endOffset);
							
							text = h1 + anchorEnd + h2;
							range.endContainer.data = text; // Replace original content with anchored one
							
						} else {
							
							// Get both parts
							h1 = range.startContainer.data.substr(0, range.startOffset);
							h2 = range.startContainer.data.substr(range.endOffset);
							hx = range.startContainer.data.substr(range.startOffset, range.endOffset - range.startOffset);
							
							range.startContainer.data = h1 + anchorStart + hx + anchorEnd + h2;
							
						}
						
					}
					
					var send = ed.getContent();
					
					// Put back original content while transaction. Sometimes content may twitch
					ed.setContent(orig); 
					ed.selection.moveToBookmark(mark);
					
					// Prepare content part
					if (sel) {
						
						var h1 = send.indexOf(anchorStart);
						var h2 = send.indexOf(anchorEnd);
						
						var part1 = send.substr(0, h1);
						var part2 = send.substr(h2 + anchorEnd.length);
						send = send.substr(h1 + anchorStart.length, h2 - h1 - anchorStart.length);
						
						// Add whitespaces if there are any
						part1 = part1 + (/^[\s]+/i.exec(send) || [""])[0];
						part2 = (/[\s]+$/i.exec(send) || [""])[0] + part2;
						
						send.replace(/^[\s]+/i, "").replace(/[\s]+$/i, "");
						
					}
					
					// Send raw XML data
					tinymce.util.XHR.send({
						type		: "POST",
						url			: url + "/typograf.php",
						data		: "<?xml version=\"1.0\" encoding=\"UTF-8\"?><document><type>" + type + "</type><text><![CDATA[" + send + "]]></text></document>",
						error		: function(e, x) { alert(e); },
						success	: function(text) {
							
							// Remove BOM
							text = text.replace(/^[\ufeff]+/g, '');
							
							if (sel) text = part1 + text + part2;
							
							// Set new contents
							ed.setContent(text);
							ed.selection.moveToBookmark(mark); // Usually it fits, because typograf only transforms entities, so the length of the text is equal
							
							if (!confirm(ed.getLang("typograf.confirm"))) { // Set original contents
								
								// Restore everything
								ed.setContent(orig);
								ed.selection.moveToBookmark(mark);
								
							}
							
							// Remove highlight from the control
							ed.controlManager.setActive("typograf", false);
							
						}
					});
					
				return true;
				break;
				
			}
			
			// Pass to next handler in chain
			return false;
			
		},
		
		// CONTROL
		
		createControl: function(n, cm) {
			
			var ed = tinyMCE.activeEditor;
			var url = this.url;
			
			switch (n) {
				case "typograf":
					
					var c = cm.createSplitButton(n, {
						title: "typograf.desc",
						image: url + "/img/typograf.gif",
						onclick: function() {
							
							ed.execCommand("mceTypograf", false, "");
							
						}
					});
					
					c.onRenderMenu.add(function(c, m) {
						m.add({
							title: "typograf.title",
							"class" : "mceMenuItemTitle"
						}).setDisabled(1);
						m.add({
							title: "typograf.lebedev",
							image: url + "/img/icon-lebedev.gif",
							onclick: function() {
								
								ed.execCommand("mceTypograf", false, "lebedev");
								
							}
						});
						m.add({
							title: 'typograf.spearance',
							image: url + "/img/icon-spearance.gif",
							onclick: function() {
								
								ed.execCommand("mceTypograf", false, "spearance");
								
							}
						});
						m.add({
							title: 'typograf.jare',
							image: url + "/img/icon-jare.gif",
							onclick: function() {
								
								ed.execCommand("mceTypograf", false, "jare");
								
							}
						});
					});
					
				return c;
				
			}
			
			return null;
			
		},		
		
		// INFO
		
		getInfo : function() {
			return {
				longname	: "Typograf",
				author		: "DiS @ Nimax Studio",
				authorurl	: "http://dis.dj",
				infourl		: "http://nimax.ru",
				version		: "2.1.1"
			};
		}
		
	});
	
	tinymce.PluginManager.add("typograf", tinymce.plugins.Typograf);
	
})();