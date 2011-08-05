// animate loading/login message
$(function() {
	if ($('#loading').length > 0) {
		$('#loading').hide();
		$('#loading').slideDown('fast');
	} else {
		$('#login').hide();
		$('#login').fadeIn('fast');
	}
});

// a recursive animation function that animates each bar on 
// a graph one by one to its correct height
function slideIn_each(first_item) {
	if ($(first_item).length > 0) {
		var count = $(first_item).dataset('count') * 5;
		$(first_item).animate(
			{height: count}, 
			100,
			function() {
				slideIn_each($(first_item).parent().next().children('.friend_count'));
			}
		);
	}
}

// load app into page
$.ajax({
	url:'ajax/app.php',
	success: function(data) {
		$('#loading').replaceWith(data);
		
		// hide all items after 10th item on longer friend list popups
		$('.section div .friend_list').each(function() {
			if ($(this).children('li').length > 10) {
				$(this).children('li:nth-child(10)').nextAll().hide();
				$(this).children('li:nth-child(10)').after('<a class="friend_list_more" href="javascript:;">more...</a>');
			}
		});
		
		// slide bars up into their correct heights by month
		slideIn_each($('#by_month div .friend_count:first'));
		
		// slide bars up into their correct heights by year
		slideIn_each($('#by_year div .friend_count:first'));
	}
});

// display full name list on click
$('.section .friend_list_more').live('click', function() {
	$(this).parent().children('li:nth-child(10)').nextAll().show();
	$(this).remove();
});