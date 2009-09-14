window.addEvent('domready', function() {
	var select = $('variation_chooser'), preview = $('variation_preview'), next = $('variation_chooser_next'), prev = $('variation_chooser_prev');
	if (select && preview && prev && next) {
		select.addEvent('change', function(e) {
			new Event(e).stop();
			selectImage(select.selectedIndex);
		});
		prev.addEvent('click', function() {
			var index = select.selectedIndex;
			if (index - 1 < 0) index = select.options.length - 1;
			else index -= 1;
			select.selectedIndex = index;
			selectImage(index);
		});
		next.addEvent('click', function() {
			var index = select.selectedIndex;
			if (index + 1 >= select.options.length) index = 0;
			else index += 1;
			select.selectedIndex = index;
			selectImage(index);
		});
		
		var asset;
		var selectImage = function(index) {
			preview.setStyle('background', 'url(images/loading.gif) center center no-repeat');
			asset = new Asset.image('images/stories/styles/ss_' + select.options[index].value + '.jpg', {
				onload: function() { 
					if (index == select.selectedIndex) preview.setStyle('background-image', 'url(' + this.src + ')');
				}
			});
		};
		
		selectImage(select.selectedIndex);
	};
});
