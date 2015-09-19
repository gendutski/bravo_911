var $content_loader = new ContentLoader('#konten-utama', '#konten-title');

function checkAll(v){
	$('#konten-utama').find('form').find('input[type=checkbox]').each(function(){
		this.checked = (v == 1? true:false);
	})
}

function checkAllDefault(){
	$('#konten-utama').find('form').find('input[type=checkbox][data-default=1]').each(function(){
		this.checked = true;
	})
}

$(document).ready(function(){
	
	$('.menu-loader').click(function(){
		
		if(!$content_loader.isLoading){
			//remove active dari semua
			$('.menu-loader').removeClass('active');
			
			//add active
			$(this).addClass('active');
			
			if($(this).attr('href') != ''){
				$content_loader.load($(this).attr('href'), $(this).data('title'));
			}
		}
		return false;
	});
	
	$('#form_profile').submit(function(){
		$content_loader.submitForm(this);
		return false;
	});
	
});
