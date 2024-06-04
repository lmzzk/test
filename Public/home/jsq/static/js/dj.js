var random = {

	ad_num : 2,

	init : function(){

		n = (Math.floor(Math.random()*random.ad_num+1));

		switch(n){

		case 1:
                               document.write('<script src="https://www.ant2.cn/static/js/bajie.js"></script>');
			break;
			case 2:
                               document.write('<script src="https://www.ant2.cn/static/js/bajie.js"></script>');
			break;
		}

	}

}

random.init();
