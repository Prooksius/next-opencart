;(function ($) {
	$.fn.closestChild = function (selector) {
		let $children, $results

		$children = this.children()

		if ($children.length === 0) return $()

		$results = $children.filter(selector)

		if ($results.length > 0) return $results
		else return $children.closestChild(selector)
	}

	function translitte(source, destination) {
		var str = source.value
		var space = '-'
		var link = ''
		var transl = {
			а: 'a',
			б: 'b',
			в: 'v',
			г: 'g',
			д: 'd',
			е: 'e',
			ё: 'e',
			ж: 'zh',
			з: 'z',
			и: 'i',
			й: 'j',
			к: 'k',
			л: 'l',
			м: 'm',
			н: 'n',
			о: 'o',
			п: 'p',
			р: 'r',
			с: 's',
			т: 't',
			у: 'u',
			ф: 'f',
			х: 'h',
			ц: 'c',
			ч: 'ch',
			ш: 'sh',
			щ: 'sh',
			ъ: space,
			ы: 'y',
			ь: space,
			э: 'e',
			ю: 'yu',
			я: 'ya',
		}

		if (str != '') str = str.toLowerCase()

		for (var i = 0; i < str.length; i++) {
			if (/[а-яё]/.test(str.charAt(i))) {
				// заменяем символы на русском
				link += transl[str.charAt(i)]
			} else if (/[a-z0-9]/.test(str.charAt(i))) {
				// символы на анг. оставляем как есть
				link += str.charAt(i)
			} else {
				if (link.slice(-1) !== space) link += space // прочие символы заменяем на space
			}
		}
		destination.value = link
	}

	function generate_string(length) {
		let result = ''
		let characters =
			'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_'
		let charactersLength = characters.length
		for (let i = 0; i < length; i++) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength))
		}
		return result
	}

	let csrfParam = $('meta[name="csrf-param"]').attr('content')
	let csrfToken = $('meta[name="csrf-token"]').attr('content')

	$(document).ready(function () {
		/*
        $(".phone").mask("+7(999)999-99-99",{placeholder:"+7(   )   -  -  "});
        $('.callback-section select').styler();

        $('.all-reviews').fancybox({});
        new WOW(
            {
                mobile: false,
            }
        ).init();
*/
		$('[rel=tooltip]').tooltip()

		$(document).delegate('.grid-editable', 'change', function () {
			let cur_input = $(this),
				cur_form = $(this).closest('form'),
				formData = cur_form.serialize(),
				action = cur_form.attr('action'),
				off_val = cur_form.attr('data-off'),
				on_val = cur_form.attr('data-on'),
				textoff = cur_form.attr('data-textoff'),
				texton = cur_form.attr('data-texton')

			if (!off_val) {
				off_val = 0
			}
			if (!on_val) {
				on_val = 1
			}
			if (!textoff) {
				textoff = 'Нет'
			}
			if (!texton) {
				texton = 'Да'
			}

			$.ajax({
				url: action + '&inplace_edit=1',
				data: formData,
				type: 'POST',
				dataType: 'json',
				success: function (data) {
					if (data['success'] === '1') {
						if (cur_form.attr('data-reload') === '1') {
							location.reload()
						} else {
							cur_input.addClass('updated')
							if (cur_input.attr('type') == 'checkbox') {
								cur_input
									.parent()
									.prev()
									.removeClass('label-success')
									.removeClass('label-danger')
									.addClass(
										data['value'] == on_val ? 'label-success' : 'label-danger'
									)
									.html(data['value'] == on_val ? texton : textoff)
							}
							cur_input
								.closest('tr')
								.removeClass('oranged')
								.addClass(data['value'] == off_val ? 'oranged' : '')
						}
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + ' - ' + xhr.statusText + ' - ' + xhr.responseText)
				},
			})
		})

		$(document).delegate('#payout-status', 'change', function () {
			if ($(this).val() == 0) {
				$(this).prev().css('background-color', 'orange')
			} else if ($(this).val() == 1) {
				$(this).prev().css('background-color', 'green')
			} else {
				$(this).prev().css('background-color', 'red')
			}
		})

		$('.seosource-input').on('change', function () {
			let seodest = $(this).closest('form').find('.seodest-input')
			if (seodest.val() == '') {
				translitte(this, seodest[0])
			}
		})

		function check_dups(cur_edit) {
			$('.promocodes-area .promocode-error').text('')
			$('.promocodes-area .promocode-add-btn').removeAttr('disabled')
			$(this).closest('form').find('[type="submit"]').removeAttr('disabled')
			$('.promocodes-area .promocodes-row input[name*="[promocode]"]').each(
				function () {
					if (!cur_edit.is($(this)) && cur_edit.val() == $(this).val()) {
						$('.promocodes-area .promocode-error').text(
							'Промокод "' + cur_edit.val() + '" уже есть в списке'
						)
						$('.promocodes-area .promocode-add-btn').attr(
							'disabled',
							'disabled'
						)
						$(this)
							.closest('form')
							.find('[type="submit"]')
							.attr('disabled', 'disabled')
					}
				}
			)
		}

		$('.promocode-add-btn').on('click', function () {
			let cont = $(this)
				.closest('.promocodes-area')
				.find('.promocodes-row:first')
			let last_cont = $(this)
				.closest('.promocodes-area')
				.find('.promocodes-row:last')
			let number = parseInt(last_cont.attr('data-number'))
			number++
			let cloned = cont.clone()
			cloned.attr('data-number', number)
			cloned.find('input').each(function () {
				let inp_name = $(this).attr('name')
				let inp_name_arr = inp_name.split('[0]')
				inp_name = 'Customer[promocodes][' + number + ']' + inp_name_arr[1]
				$(this).attr('name', inp_name)
				if (inp_name_arr[1] == '[promocode]') {
					$(this).val(generate_string(12))
				}
				if (inp_name_arr[1] == '[discount]') {
					$(this).val(0)
				}
				if (inp_name_arr[1] == '[name]') {
					$(this).val('<новое название>')
				}
			})
			cloned.insertAfter(last_cont).find('input:first').focus().select()
			if ($('.promocodes-rows .promocodes-row').length > 1) {
				$('.promocodes-row .promocode-delete-btn').removeAttr('disabled')
			}
		})

		$(document).delegate(
			'.promocodes-row .promocode-delete-btn',
			'click',
			function () {
				$('.promocodes-area .promocode-error').text('')
				$('.promocodes-area .promocode-add-btn').removeAttr('disabled')
				$(this).closest('form').find('[type="submit"]').removeAttr('disabled')
				$(this).closest('.row').remove()
				if ($('.promocodes-rows .promocodes-row').length < 2) {
					$('.promocodes-row .promocode-delete-btn').attr(
						'disabled',
						'disabled'
					)
				}
			}
		)
		$(document).delegate(
			'.promocodes-row input[name*="[promocode]"]',
			'change',
			function () {
				let cur_value = $(this).val()
				let cur_edit = $(this)
				check_dups(cur_edit)
			}
		)

		$('.customer-tree .open-levels').on('click', function () {
			let container = $(this)
				.closest('.customer-tree')
				.find('.referals-container')
			container.find('.open-childs.fa-plus-square-o:visible').each(function () {
				$(this).trigger('quickclick')
			})
		})
		$('.customer-tree .close-levels').on('click', function () {
			let container = $(this)
				.closest('.customer-tree')
				.find('.referals-container')
			$(
				container.find('.open-childs.fa-minus-square-o:visible').get().reverse()
			).each(function () {
				$(this).trigger('quickclick')
			})
		})

		$(document).delegate(
			'.customer-tree .referals-container .childs',
			'expanded',
			function (e) {
				e.stopPropagation()
				console.log($(this))
				$(this)
					.find('.open-childs.fa-plus-square-o')
					.each(function () {
						$(this).trigger('quickclick')
					})
			}
		)

		$(document).delegate(
			'.client-area .open-childs',
			'click quickclick',
			function (e) {
				let etype = e.type
				let plus = $(this)
				let cur_tr = $(this).closest('.data-line')
				let level = cur_tr.attr('data-level')
				let childs_cont = cur_tr.next().filter('.childs')
				let this_id = cur_tr.closest('.referals-container').attr('data-id')

				if (plus.hasClass('fa-plus-square-o')) {
					if (!childs_cont.length) {
						let parent_id = cur_tr.attr('data-id')

						$.ajax({
							url: '/admin/customer/get-childs',
							dataType: 'html',
							type: 'get',
							data: {
								csrfParam: csrfToken,
								this_id: this_id,
								id: parent_id,
								level: level,
							},
							success: function (result) {
								if (result) {
									plus
										.removeClass('fa-plus-square-o')
										.addClass('fa-minus-square-o')
									let childs = $(result).hide().insertAfter(cur_tr)
									if (etype === 'quickclick') {
										childs.show(10, function () {
											childs.trigger('expanded')
										})
									} else {
										childs.slideDown()
									}
								}
							},
							error: function (xhr, ajaxOptions, thrownError) {
								alert(thrownError + xhr.statusText + xhr.responseText)
							},
						})
					} else {
						plus.removeClass('fa-plus-square-o').addClass('fa-minus-square-o')
						if (etype === 'quickclick') {
							childs_cont.closestChild('.childs-cont').show(10, function () {
								childs_cont.trigger('expanded')
							})
						} else {
							childs_cont.closestChild('.childs-cont').slideDown()
						}
					}
				} else {
					plus.removeClass('fa-minus-square-o').addClass('fa-plus-square-o')
					if (etype === 'quickclick') {
						childs_cont.closestChild('.childs-cont').hide(10)
					} else {
						childs_cont.closestChild('.childs-cont').slideUp()
					}
				}
			}
		)
	})
})(jQuery)
