/**
 * Created by edwardstock on 21.03.14.
 *
 *
 */

$(function(){
	$('div.wrap').on('click','div.group-item', function(event) {
		event.preventDefault();

		var item = $(this);
		var storages = item.find('div.group-storages');

		if(!storages.hasClass('opened')) {
			storages.animate({
				height: '100%',
				opacity: '1',
				padding: '5px'
			}, 250).addClass('opened');
		} else {
			storages.animate({
				height: '0',
				opacity: '0',
				padding: '0'
			}, 250).removeClass('opened');
		}


	});
});
