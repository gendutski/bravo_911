/**
 * class untuk menggenerate content via ajax
 * 
 * @param	string (#id, .class, [attr]) atau dom object atau jquery object
 * @param	string (#id, .class, [attr]) atau dom object atau jquery object
 */

var ContentLoader = function(box_element, title_element)
{
	var isLoading = false;
	
	
	/**
	 * delete data
	 * @param	string endpoint
	 * @param	string token
	 * @return	void
	 */
	var deleteData = function(url, token){
		var cls = this;
		if(!confirm('Apakah anda yakin untuk menghapus data ini?')){
			return;
		}
		
		$.ajax({
			type: 'post', 
			url: url, 
			data: {
				"_token":token,
				"_method":'DELETE'
			},
			cache: false,
			dataType: 'json',
			success: function($response, $textStatus, $jqXHR) {
				$(box_element).find('form').submit();
			},
			beforeSend: function($jqXHR, $textStatus){
				//set loading
				cls.isLoading = true;
				
				//disable all form elements
				try{
					var form = $(box_element).find('form').get(0);
					$.each(form.elements, function(i, itm){
						$(itm).attr('disabled', true);
					});
				} catch(err){
					
				}
				
				//remove alert
				if($(box_element).next().attr('data-type') == 'misc-box-element'){
					$(box_element).next().remove();
				}
				
				//append loading bar
				$(box_element).after('<div class="row" data-type="misc-box-element" style="margin-top:1em">\
					<div class="col-lg-12">\
						<div class="progress">\
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">\
								Deleting...\
							</div>\
						</div>\
					</div>\
				</div>');
			},
			error: function ($jqXHR, $textStatus, $errorThrown) {
				var $message = $errorThrown;
				
				if($jqXHR.status == 400){
					//bad request
					try{
						var $err_msg = $.parseJSON($jqXHR.responseText);
						if(typeof($err_msg.error) != 'undefined'){
							$message = $err_msg.error;
						}
					} catch(err){
						//
					}
				} else if($jqXHR.status == 401){
					//Unauthorized
					window.location.reload();
					return; 
				} else if($jqXHR.status == 422){
					//Unprocessable Entity (WebDAV)
					$message = 'Gagal memproses data :';
					$message += '<ul>';
					
					var $err_msg = $.parseJSON($jqXHR.responseText);
					
					for(x in $err_msg){
						$.each($err_msg[x], function(i, itm){
							$message += '<li>';
							$message += itm;
							$message += '</li>';
						});
					}
					
					$message += '</ul>';
				}
				
				//remove loading box
				if($(box_element).next().attr('data-type') == 'misc-box-element'){
					$(box_element).next().remove();
				}
				
				//alert error
				$(box_element).after('<div class="row" data-type="misc-box-element" style="margin-top:1em">\
					<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' + $message + '</div>\
				</div>');
				
				//enable all form elements
				try{
					var form = $(box_element).find('form').get(0);
					$.each(form.elements, function(i, itm){
						$(itm).removeAttr('disabled');
					});
				} catch(err){
					
				}
			},
			complete: function($jqXHR, $textStatus){
				//unset loading
				cls.isLoading = false;
			}
		});
	}
	
	
	/**
	 * get data
	 * 
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	object
	 * @return	void
	 */
	var load = function(url, title, data)
	{
		var cls = this;
		
		//remove alert & loading box
		if($(box_element).next().attr('data-type') == 'misc-box-element'){
			$(box_element).next().remove();
		}
		
		$.ajax({
			type: 'get', 
			url: url, 
			data: (data || {}),
			cache: false,
			dataType: 'json',
			success: function($response, $textStatus, $jqXHR) {
				//render form
				cls.renderForm($response.form, $response.form_data);
				
				if($response.type == 'table list'){
					cls.renderTable($response.table_header, $response.table_data, $response.total_records, $response.total_page);
				}
			},
			beforeSend: function($jqXHR, $textStatus){
				//set loading
				cls.isLoading = true;
				
				//set title
				$(title_element).html(title);
				
				//set loading bar
				$(box_element).html('<div class="col-lg-12">\
					<div class="progress">\
						<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">\
							Loading...\
						</div>\
					</div>\
				</div>');
			},
			error: function ($jqXHR, $textStatus, $errorThrown) {
				var $message = $errorThrown;
				
				if($jqXHR.status == 400){
					try{
						var $err_msg = $.parseJSON($jqXHR.responseText);
						if(typeof($err_msg.error) != 'undefined'){
							$message = $err_msg.error;
						}
					} catch(err){
						//
					}
				} else if($jqXHR.status == 401){
					//Unauthorized
					window.location.reload();
					return; 
				}
				
				
				$(box_element).html('<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' + $message + '</div>');
			},
			complete: function($jqXHR, $textStatus){
				//unset loading
				cls.isLoading = false;
			}
		});
	}
	
	
	/**
	 * set load page
	 * @access	public
	 * @param	numeric
	 * @return	boolean
	 */
	var loadPage = function(page){
		$(box_element).find('form').find('input[type=hidden][name=page]').val(page);
		$(box_element).find('form').submit();
		
		return false;
	}
	
	/**
	 * render form input/search
	 * 
	 * @access	public
	 * @param	object response
	 * @return	void
	 */
	var renderForm = function($form, $form_data){
		var cls = this;
		$form_data = $form_data || {};
		
		//crete form element
		var form = document.createElement('form');
		$(form).attr($form.attr);
		
		//create hidden input
		if(typeof($form.hidden) != 'undefined' && $form.hidden.length > 0){
			$.each($form.hidden, function(i, itm){
				var input = document.createElement('input');
				$(form).append(input);
				$(input).attr(itm);
				$(input).attr('type', 'hidden');
				
				//set value
				if(typeof(itm.value) == 'undefined' && typeof($form_data[itm.name]) != 'undefined'){
					$(input).val($form_data[itm.name]);
				}
			});
		}
		
		//create elements block
		if(typeof($form.elements) != 'undefined' && $form.elements.length > 0)
		{
			for(var $i=0; $i<$form.elements.length; $i++){
				if($form.elements[$i].length <= 12){
					var tampung = 12/$form.elements[$i].length;
					var css_class = 'col-lg-'+tampung;
					
					for(var $j=0; $j<$form.elements[$i].length; $j++){
						var itm = $form.elements[$i][$j];
						
						//create div block
						var div = document.createElement('div');
						$(form).append(div);
						
						//set div class
						$(div).addClass(css_class);
						
						//render element
						$.each(itm, function(j, elm){
							
							//element
							if(elm.element == 'button'){
								var element = document.createElement(elm.element);
								$(element).attr(elm.attr);
								$(element).html(elm.label);
								
								//append data
								$(div).append(element);
								
								//redirect endpoint
								if(typeof(elm.redirect) == 'object'){
									$(element).attr({
										'data-redirect':elm.redirect.url + '?' + $.param(elm.redirect.param)
									});
								}
								
								//link button
								if($(element).attr('data-endpoint')){
									$(element).click(function(){
										if($(this).attr('data-method').toLowerCase() == 'delete'){
											cls.deleteData($(this).attr('data-endpoint'), $(this).attr('data-token'));
										} else {
											cls.load(
												$(this).attr('data-endpoint'),
												$(this).attr('data-title'),
												{'redirect':$(this).attr('data-redirect')}
											);
										}
										
										return false;
									});
								}
							} else if(elm.element == 'inline-checkbox' || elm.element == 'checkbox'){
								//create div element
								var div2 = document.createElement('div');
								$(div).append(div2);
								$(div2).addClass('form-group');
								
								//create label
								var label = document.createElement('label');
								$(div2).append(label);
								$(label).html(elm.label);
								
								//create element
								if(elm.element == 'inline-checkbox'){
									//create div element
									var div3 = document.createElement('div');
									$(div2).append(div3);
									$(div3).addClass('checkbox');
									
									$.each(elm.options, function(m, chk){
										//label
										var label2 = document.createElement('label');
										$(label2).addClass('checkbox-inline');
										$(div3).append(label2);
										
										//checkbox
										var element = document.createElement('input');
										$(label2).append(element);
										$(label2).append(chk.label);
										$(element).attr({
											"type":'checkbox',
											"name":chk.name,
											"value":chk.value,
											"data-default":chk.default
										});
										
										//oncheck
										$(element).change(function(){
											if($(this).attr('data-default') == 1 && !this.checked){
												$(this).parent().siblings().find('input[type=checkbox]').removeAttr('checked');
											} else if($(this).attr('data-default') == 0 && this.checked){
												$(this).parent().siblings().find('input[type=checkbox][data-default=1]').attr('checked', true);
											}
										});
									});
								} else {
									$.each(elm.options, function(m, chk){
										//create div element
										var div3 = document.createElement('div');
										$(div2).append(div3);
										$(div3).addClass('checkbox');
										
										//label
										var label2 = document.createElement('label');
										$(div3).append(label2);
										
										//checkbox
										var element = document.createElement('input');
										$(label2).append(element);
										$(label2).append(chk.label);
										$(element).attr({
											"type":'checkbox',
											"name":chk.name,
											"value":chk.value,
											"data-default":chk.default
										});
										
										//set value
										if(typeof($form_data[chk.name]) != 'undefined' && Object.prototype.toString.call($form_data[chk.name]) ==='[object Array]'){
											if($form_data[chk.name].indexOf(chk.value) >= 0){
												element.checked = true;
											}
										}
										
										//oncheck
										$(element).change(function(){
											if($(this).attr('data-default') == 1 && !this.checked){
												$(this).parent().parent().siblings().find('input[type=checkbox]').each(function(){
													this.checked = false
												});
											} else if($(this).attr('data-default') == 0 && this.checked){
												$(this).parent().parent().siblings().find('input[type=checkbox][data-default=1]').each(function(){
													this.checked = true;
												});
											}
										});
									});
								}
							} else if(elm.element == 'datepicker_range'){
								//create div element
								var div2 = document.createElement('div');
								$(div).append(div2);
								$(div2).addClass('form-group');
								
								//create label
								var label = document.createElement('label');
								$(div2).append(label);
								$(label).html(elm.label);
								
								//create div 
								var div3 = document.createElement('div');
								$(div2).append(div3);
								$(div3).addClass('input-daterange input-group');
								
								//start date
								var element1 = document.createElement('input');
								$(element1).addClass('input-sm form-control');
								$(element1).attr({'name':elm.attr.name+'[]', 'type':'text'});
								$(div3).append(element1);
								
								//span to
								var span = document.createElement('span');
								$(span).addClass('input-group-addon');
								$(span).html('s/d');
								$(div3).append(span);
								
								//end date
								var element2 = document.createElement('input');
								$(element2).addClass('input-sm form-control');
								$(element2).attr({'name':elm.attr.name+'[]', 'type':'text'});
								$(div3).append(element2);
								
								//set datepicker
								$(element1).datepicker({
									format: "yyyy-mm-dd",
									clearBtn: true,
									language: "id",
									autoclose: true
								});
								$(element2).datepicker({
									format: "yyyy-mm-dd",
									clearBtn: true,
									language: "id",
									autoclose: true
								});
								
								//set element value
								try{
									if(typeof(elm.attr.value) == 'undefined' && typeof($form_data[elm.attr.name][0]) != 'undefined'){
										$(element1).val($form_data[elm.attr.name][0]);
									}
									if(typeof(elm.attr.value) == 'undefined' && typeof($form_data[elm.attr.name][1]) != 'undefined'){
										$(element2).val($form_data[elm.attr.name][1]);
									}
								} catch(err){
									
								}
							} else if(elm.element == 'header'){
								var element = document.createElement('h3');
								$(element).html(elm.label);
								$(element).addClass('page-header');
								
								if(typeof(elm.attr) != 'undefined'){
									$(element).attr(elm.attr);
								}
								
								//append data
								$(div).append(element);
							} else {
								//create div element
								var div2 = document.createElement('div');
								$(div).append(div2);
								$(div2).addClass('form-group');
								
								//create label
								var label = document.createElement('label');
								$(div2).append(label);
								$(label).html(elm.label);
								
								//create element
								if(elm.element == 'select'){
									var element = document.createElement('select');
									
									//index 0
									var option = document.createElement('option');
									$(option).attr('value', '');
									$(option).html('');
									$(element).append(option);
									
									if(Object.prototype.toString.call(elm.options) ==='[object Array]' && elm.options.length > 0){
										$.each(elm.options, function(k, opt){
											var option = document.createElement('option');
											$(option).attr(opt.attr);
											$(option).html(opt.html);
											$(element).append(option);
										});
									} else if(Object.prototype.toString.call(elm.optgroup) ==='[object Array]' && elm.optgroup.length > 0){
										$.each(elm.optgroup, function(k, optg){
											var optgroup = document.createElement('optgroup');
											$(optgroup).attr('label', optg.label);
											$(element).append(optgroup);
											
											$.each(optg.options, function(l, opt){
												var option = document.createElement('option');
												
												$(option).attr(opt.attr);
												$(option).html(opt.html);
												$(optgroup).append(option);
											});
										});
									}
									
									
								} else if(elm.element == 'textarea') {
									var element = document.createElement('textarea');
								} else {
									var element = document.createElement('input');
								}
								$(element).addClass('form-control');
								$(element).attr(elm.attr);
								$(div2).append(element);
								
								//datepicker?
								if(elm.element == 'datepicker'){
									//set datepicker
									$(element).datepicker({
										format: "yyyy-mm-dd",
										clearBtn: true,
										language: "id",
										autoclose: true
									});
								}
								
								//number format
								if(elm.element == 'number_format'){
									$(element).number( true, 0 );
								}
								
								//set element value
								if(typeof(elm.attr.value) == 'undefined' && typeof($form_data[elm.attr.name]) != 'undefined'){
									if(Object.prototype.toString.call($form_data[elm.attr.name]) ==='[object Array]'){
										var _t = $form_data[elm.attr.name].shift();
										$(element).val(_t);
									} else {
										$(element).val($form_data[elm.attr.name]);
									}
								}
							}
						});
					}
					
					//clear float
					var div = document.createElement('div');
					$(div).addClass('clearfix visible-lg-block');
					$(form).append(div);
				}
			}
		}
		
		//set event form submit
		$(form).submit(function(){
			if($(this).attr('data-type') == 'table list'){
				cls.load($(this).attr('data-endpoint'), $(this).attr('data-title'), $(this).serialize());
			} else {
				cls.submitForm(this);
			}
			return false;
		});
		
		//write to box
		$(box_element).html(form);
	}
	
	
	/**
	 * render table
	 * 
	 * @access	public
	 * @param	object
	 * @param	object
	 * @return 	void
	 */
	var renderTable = function($header, $data, $total_records, $total_page){
		var cls = this;
		$total_records = $total_records || 0;
		$total_page = $total_page || 1;
		
		//box_row
		var box_row = document.createElement('div');
		$(box_element).append(box_row);
		$(box_row).addClass('row');
		
		if($data.length > 0){
			//box
			var div = document.createElement('div');
			$(box_row).append(div);
			$(div).addClass('col-lg-12');
			
			//panel
			var div_panel = document.createElement('div');
			$(div).append(div_panel);
			$(div_panel).addClass('panel-body');
			
			//responsive
			var div_responsive = document.createElement('div');
			$(div_panel).append(div_responsive);
			$(div_responsive).addClass('table-responsive');
			
			//table
			var table = document.createElement('table');
			$(div_responsive).append(table);
			$(table).addClass('table table-striped table-bordered table-hover dataTable no-footer');
			
			
			//------header start {
			//thead
			var thead = document.createElement('thead');
			$(table).append(thead);
			
			for(x in $header){
				
				//tr
				var tr = document.createElement('tr');
				$(tr).attr('role', 'row');
				$(thead).append(tr);
				
				$.each($header[x], function(i, itm){
					var th = document.createElement('th');
					$(tr).append(th);
					$(th).html(itm.title);
					
					//set width
					if(typeof(itm.width) != 'undefined'){
						$(th).css({'width':itm.width});
					}
					
					//colspan
					if(typeof(itm.colspan) != 'undefined'){
						$(th).attr('colspan', itm.colspan);
					}
					
					//rowspan
					if(typeof(itm.rowspan) != 'undefined'){
						$(th).attr('rowspan', itm.rowspan);
					}
					
					//css
					if(typeof(itm.css) != 'undefined'){
						$(th).css(itm.css);
					}
					
					//sort type
					if(typeof(itm.id) != 'undefined' && itm.id != ''){
						$(th).css({'cursor':'pointer'});
						
						if(itm.is_sort == ''){
							$(th).addClass('sorting');
							$(th).attr({
								'data-id':itm.id,
								'data-sort':''
							});
						} else {
							$(th).addClass('sorting_'+itm.is_sort);
							$(th).attr({
								'data-id':itm.id,
								'data-sort':itm.is_sort
							});
						}
						
						$(th).click(function(){
							cls.sort(this);
							return false;
						});
					}
				});
			}
			//------header end	}
			
			
			
			//------data start {
			//create tbody
			var tbody = document.createElement('tbody');
			$(table).append(tbody);
			
			$.each($data, function(j, row){
				//create tr
				var tr = document.createElement('tr');
				$(tbody).append(tr);
				
				$.each(row, function(j, itm){
					var td = document.createElement('td');
					$(tr).append(td);
					
					//css
					if(typeof(itm.css) != 'undefined'){
						$(td).css(itm.css);
					}
					
					if(itm.type == 'link' && typeof(itm.attr) != 'undefined'){
						var a = document.createElement('a');
						$(a).attr({
							'href':'/'
						});
						$(a).attr(itm.attr);
						$(a).html(itm.text);
						$(td).append(a);
						
						//onclick event
						$(a).click(function(){
							if($(this).attr('data-method').toLowerCase() == 'delete'){
								cls.deleteData($(this).attr('data-endpoint'), $(this).attr('data-token'));
							} else {
								cls.load(
									$(this).attr('data-endpoint'), 
									$(this).attr('data-method'), 
									$(this).attr('data-title'), 
									{}
								);
							}
							return false;
						});
					} else if(itm.type == 'buttons' && typeof(itm.list) != 'undefined' && Object.prototype.toString.call(itm.list) ==='[object Array]'){
						$.each(itm.list, function(k, but){
							var button = document.createElement('button');
							$(td).append(button);
							$(button).addClass('btn btn-default btn-circle');
							
							if(typeof(but.text) != 'undefined'){
								$(button).html(but.text);
							}
							
							if(typeof(but.redirect) == 'object'){
								$(button).attr({
									'data-redirect':but.redirect.url + '?' + $.param(but.redirect.param)
								});
							}
							
							if(typeof(but.attr) == 'object'){
								$(button).attr(but.attr);
								
								//onclick event
								$(button).click(function(){
									if($(this).attr('data-method').toString().toLowerCase() == 'delete'){
										cls.deleteData($(this).attr('data-endpoint'), $(this).attr('data-token'));
									} else {
										if($(this).attr('data-redirect')){
											var data = {'redirect':$(this).attr('data-redirect')};
										} else {
											var data = {};
										}
										
										cls.load(
											$(this).attr('data-endpoint'), 
											$(this).attr('data-title'), 
											data
										);
									}
									return false;
								});
							}
						});
						
					} else {
						$(td).html(itm.text);
					}
				});
			});
			//------data end }
			
			//------footer start{
			var div_footer = document.createElement('div');
			$(div_footer).addClass('row');
			$(div_panel).append(div_footer);
			
			//total page start {
			var div = document.createElement('div');
			$(div).addClass('col-sm-6');
			$(div_footer).append(div);
			
			var div2 = document.createElement('div');
			$(div2).addClass('dataTables_info');
			$(div2).attr({
				"role":'alert',
				"aria-live":'polite',
				'aria-relevant':'all'
			});
			$(div2).html('Total ' + $total_records + ($total_records>1? ' records':' record'));
			$(div).append(div2);
			//total page end }
			
			
			//paging start {
			if($total_page > 1){
				var div = document.createElement('div');
				$(div).addClass('col-sm-6');
				$(div_footer).append(div);
				
				var div2 = document.createElement('div');
				$(div2).addClass('dataTables_paginate paging_simple_numbers');
				$(div2).css({
					"text-align":'right'
				});
				$(div).append(div2);
				
				var ul = document.createElement('ul');
				$(ul).addClass('pagination');
				$(div2).append(ul);
				
				//nampilkan beberapa button page(tidak semua)
				var page_before = 2;
				var page_after = 2;
				var current_page = $(box_element).find('form').find('input[type=hidden][name=page]').val() || 1;
				current_page = parseInt(current_page);
				
				var start_page = current_page - page_before;
				if(start_page < 1){
					page_after = page_after - (start_page - 1);
					start_page = 1;
				}
				
				var end_page = current_page + page_after;
				if(end_page > $total_page){
					start_page = start_page - (end_page - $total_page);
					end_page = $total_page;
					if(start_page < 1){ start_page = 1; }
				}
				
				//first page
				var li = document.createElement('li');
				$(li).addClass('paginate_button previous');
				$(li).attr('aria-controls', 'dataTables-example');
				$(ul).append(li);
				
				var a = document.createElement('a');
				$(li).append(a);
				$(a).attr({
					"href":'#',
					"data-page":1
				});
				if(current_page == 1){
					$(li).addClass('disabled');
					$(a).click(function(){
						return false;
					});
				} else {
					$(a).click(function(){
						return cls.loadPage($(this).attr('data-page'));
					});
				}
				$(a).html('First');
				
				//prev page
				var li = document.createElement('li');
				$(li).addClass('paginate_button previous');
				$(li).attr('aria-controls', 'dataTables-example');
				$(ul).append(li);
				
				var a = document.createElement('a');
				$(li).append(a);
				$(a).attr({
					"href":'#',
					"data-page":(current_page-1)
				});
				if(current_page == 1){
					$(li).addClass('disabled');
					$(a).click(function(){
						return false;
					});
				} else {
					$(a).click(function(){
						return cls.loadPage($(this).attr('data-page'));
					});
				}
				$(a).html('Previous');
				
				//pages
				for(pg = start_page; pg <= end_page; pg++){
					var li = document.createElement('li');
					$(li).addClass('paginate_button');
					if(pg == current_page){
						$(li).addClass('active');
					}
					$(li).attr('aria-controls', 'dataTables-example');
					$(ul).append(li);
					
					var a = document.createElement('a');
					$(li).append(a);
					$(a).attr({
						"href":'#',
						"data-page":pg
					});
					$(a).click(function(){
						return cls.loadPage($(this).attr('data-page'));
					});
					$(a).html(pg);
				}
				
				//next page
				var li = document.createElement('li');
				$(li).addClass('paginate_button next');
				$(li).attr('aria-controls', 'dataTables-example');
				$(ul).append(li);
				
				var a = document.createElement('a');
				$(li).append(a);
				$(a).attr({
					"href":'#',
					"data-page":(current_page+1)
				});
				if(current_page == $total_page){
					$(li).addClass('disabled');
					$(a).click(function(){
						return false;
					});
				} else {
					$(a).click(function(){
						return cls.loadPage($(this).attr('data-page'));
					});
				}
				$(a).html('Next');
				
				//last page
				var li = document.createElement('li');
				$(li).addClass('paginate_button next');
				$(li).attr('aria-controls', 'dataTables-example');
				$(ul).append(li);
				
				var a = document.createElement('a');
				$(li).append(a);
				$(a).attr({
					"href":'#',
					"data-page":$total_page
				});
				if(current_page == $total_page){
					$(li).addClass('disabled');
					$(a).click(function(){
						return false;
					});
				} else {
					$(a).click(function(){
						return cls.loadPage($(this).attr('data-page'));
					});
				}
				$(a).html('Last');
			}
			//paging end }
			
			//------footer end }
		} else {
			$(box_row).append('<div class="col-lg-12" style="margin-top:.5em">\
				<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Data tidak di temukan</div>\
			</div>')
		}
	}
	
	
	/**
	 * sort data, event clicked by th
	 * 
	 * @access	public
	 * @param	html object
	 * @return	void
	 */
	var sort = function(obj){
		var cls = this;
		var form = $(box_element).find('form');
		
		//set order by
		form.find('input[type=hidden][name=ord]').val($(obj).attr('data-id'));
		
		//set sort by
		if($(obj).attr('data-sort') == 'asc'){
			form.find('input[type=hidden][name=srt]').val('desc');
		} else {
			form.find('input[type=hidden][name=srt]').val('asc');
		}
		
		cls.load(form.attr('data-endpoint'), form.attr('data-title'), form.serialize());
	}
	
	/**
	 * form submit event
	 * 
	 * @access	public
	 * @param	form element
	 * @return 	void
	 */
	var submitForm = function(form){
		var cls = this;
		
		if($(form).attr('method').toLowerCase() == 'put'){
			var input = $(form).find('input[type=hidden][name=_method]');
			if(input.length == 0){
				$(form).append('<input type="hidden" name="_method" value="PUT" />');
			} else {
				input.val('PUT');
			}
			var method = 'post'
		} else {
			var method = form.method;
		}
		
		$.ajax({
			type: method, 
			url: $(form).attr('action'), 
			data: $(form).serialize(),
			cache: false,
			dataType: 'json',
			success: function($response, $textStatus, $jqXHR) {
				//remove loading box
				if($(box_element).next().attr('data-type') == 'misc-box-element'){
					$(box_element).next().remove();
				}
				
				if($response.url != ''){
					window.location.href = $response.url;
				} else {
					cls.load($response.api_endpoint, $response.title);
				}
			},
			beforeSend: function($jqXHR, $textStatus){
				//set loading
				cls.isLoading = true;
				
				//disable all form elements
				$.each(form.elements, function(i, itm){
					$(itm).attr('disabled', true);
				});
				
				//remove alert
				if($(box_element).next().attr('data-type') == 'misc-box-element'){
					$(box_element).next().remove();
				}
				
				//append loading bar
				$(box_element).after('<div class="row" data-type="misc-box-element" style="margin-top:1em">\
					<div class="col-lg-12">\
						<div class="progress">\
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">\
								Saving...\
							</div>\
						</div>\
					</div>\
				</div>');
			},
			error: function ($jqXHR, $textStatus, $errorThrown) {
				var $message = $errorThrown;
				
				if($jqXHR.status == 400){
					//bad request
					try{
						var $err_msg = $.parseJSON($jqXHR.responseText);
						if(typeof($err_msg.error) != 'undefined'){
							$message = $err_msg.error;
						}
					} catch(err){
						//
					}
				} else if($jqXHR.status == 401){
					//Unauthorized
					window.location.reload();
					return; 
				} else if($jqXHR.status == 422){
					//Unprocessable Entity (WebDAV)
					$message = 'Gagal memproses data :';
					$message += '<ul>';
					
					var $err_msg = $.parseJSON($jqXHR.responseText);
					
					for(x in $err_msg){
						$.each($err_msg[x], function(i, itm){
							$message += '<li>';
							$message += itm;
							$message += '</li>';
						});
					}
					
					$message += '</ul>';
				}
				
				//remove loading box
				if($(box_element).next().attr('data-type') == 'misc-box-element'){
					$(box_element).next().remove();
				}
				
				//alert error
				$(box_element).after('<div class="row" data-type="misc-box-element" style="margin-top:1em">\
					<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' + $message + '</div>\
				</div>');
				
				//enable all form elements
				$.each(form.elements, function(i, itm){
					$(itm).removeAttr('disabled');
				});
			},
			complete: function($jqXHR, $textStatus){
				//unset loading
				cls.isLoading = false;
			}
		});
		
	}
	
	
	return {
		deleteData:deleteData,
		isLoading:isLoading,
		load:load,
		loadPage:loadPage,
		renderForm:renderForm,
		renderTable:renderTable,
		sort:sort,
		submitForm:submitForm
	};
}
