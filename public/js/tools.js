function round(value, decimals) {
  return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function explode(delimiter, string){
	var ret = [];
	if (typeof string !== 'undefined' && typeof string === 'string' && string)
		ret = string.split(delimiter);
	return ret;
}

function in_array(val, arr){
	if (typeof arr === 'undefined' || !Array.isArray(arr) || arr.length == 0)
		return false;
	else if (typeof val === 'undefined')
		return false;
	else{
		var index = arr.indexOf(val);
		if (index < 0)
			return false;
		else
			return true;
	}
}	

function isInteger(str){
    var n = ~~Number(str);
    return String(n) === str;	
}	

function getRandom(min, max) {
    return min + Math.floor(Math.random() * (parseInt(max) - parseInt(min) + 1));
}

function getRandomFloat(min, max, digits){
	return (Math.random() * (max - min) + min).toFixed(digits)
}

//index: pos of element (1,2,3...)
function getPartStr(str, seperator, index){
	if (str && seperator){
		var temp = str.split(seperator);
		return temp[index - 1];
	}	
	return null;
}

function jhash(s){
		var a, i, j, c, c0, c1, c2, r;
		var _s = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_$';
		var _r64 = function(s, b)
		{
			return ((s | (s << 6)) >>> (b % 6)) & 63;
		};
		a = [];
		c = [];
		for (i = 0; i < s.length; i++)
		{
			c0 = s.charCodeAt(i);
			if (c0 & ~255)
			{
				c0 = (c0 >> 8) ^ c0;
			}
			c.push(c0);
			if (c.length == 3 || i == s.length - 1)
			{
				while(c.length < 3)
				{
					c.push(0);
				}
				a.push((c[0] >> 2) & 63);
				a.push(((c[1] >> 4) | (c[0] << 6)) & 63);
				a.push(((c[1] << 4) | (c[2] >> 2)) & 63);
				a.push(c[2] & 63);
				c = [];
			}
		}
		while (a.length < 16)
		{
			a.push(0);
		}
		r = 0;
		for (i = 0; i < a.length; i++)
		{
			r ^= (_r64(a[i] ^ (r | i), i) ^ _r64(i, r)) & 63;
		}
		for (i = 0; i < a.length; i++)
		{
			a[i] = (_r64((r | i & a[i]), r) ^ a[i]) & 63;
			r += a[i];
		}
		for (i = 16; i < a.length; i++)
		{
			a[i % 16] ^= (a[i] + (i >>> 4)) & 63;
		}
		for (i = 0; i < 16; i++)
		{
			a[i] = _s.substr(a[i], 1);
		}
		a = a.slice(0, 16).join('');
		return a;
}


/*******************************************/

function preProcessShareFromQuery(market, data){
	var float_range = 0.02; //可以改动

	var ret = {
		opening_price: 0,
		closing_price: 0,
		current_price: 0,
		highest_price: 0,
		lowest_price: 0,
		bid_price: 0,
		asked_price: 0,

		trade_volume: 0,
		trade_amount: 0,
		buying_list_volume1: 0,
		buying_list_price1: 0,
		buying_list_volume2: 0,
		buying_list_price2: 0,							
		buying_list_volume3: 0,
		buying_list_price3: 0,
		buying_list_volume4: 0,
		buying_list_price4: 0,
		buying_list_volume5: 0,
		buying_list_price5: 0,

		selling_list_volume1: 0,
		selling_list_price1: 0,
		selling_list_volume2: 0,
		selling_list_price2: 0,
		selling_list_volume3: 0,
		selling_list_price3: 0,
		selling_list_volume4: 0,
		selling_list_price4: 0,
		selling_list_volume5: 0,
		selling_list_price5: 0,															

		update_date: '',
		update_time: ''			
	};

	if(market == 'sh' || market == 'sz'){
		ret.opening_price = parseFloat(data[1]);
		ret.closing_price = parseFloat(data[2]);
		ret.current_price = parseFloat(data[3]);
		ret.highest_price = parseFloat(data[4]);
		ret.lowest_price = parseFloat(data[5]);
		ret.bid_price = parseFloat(data[6]);
		ret.asked_price = parseFloat(data[7]);
		ret.trade_volume = parseFloat(data[8]);
		ret.trade_amount = parseFloat(data[9]);

		ret.buying_list_volume1 = parseFloat(data[10]);
		ret.buying_list_price1 = parseFloat(data[11]);
		ret.buying_list_volume2 = parseFloat(data[12]);
		ret.buying_list_price2 = parseFloat(data[13]);							
		ret.buying_list_volume3 = parseFloat(data[14]);
		ret.buying_list_price3 = parseFloat(data[15]);
		ret.buying_list_volume4 = parseFloat(data[16]);
		ret.buying_list_price4 = parseFloat(data[17]);
		ret.buying_list_volume5 = parseFloat(data[18]);
		ret.buying_list_price5 = parseFloat(data[19]);

		ret.selling_list_volume1 = parseFloat(data[20]);
		ret.selling_list_price1 = parseFloat(data[21]);
		ret.selling_list_volume2 = parseFloat(data[22]);
		ret.selling_list_price2 = parseFloat(data[23]);
		ret.selling_list_volume3 = parseFloat(data[24]);
		ret.selling_list_price3 = parseFloat(data[25]);
		ret.selling_list_volume4 = parseFloat(data[26]);
		ret.selling_list_price4 = parseFloat(data[27]);
		ret.selling_list_volume5 = parseFloat(data[28]);
		ret.selling_list_price5 = parseFloat(data[29]);																

		ret.update_date = data[30];
		ret.update_time = data[31];	

	}

	else if (market == 'ny' || market == 'nas'){
		ret.opening_price = round(parseFloat(data[5]), 2); //
		ret.closing_price = round(parseFloat(data[26]), 2); //
		ret.current_price = round(parseFloat(data[1]), 2); //
		ret.highest_price = round(parseFloat(data[6]), 2); //
		ret.lowest_price = round(parseFloat(data[7]), 2); //

		//if(ret.current_price - 1 >= 0)
		ret.bid_price = getRandomFloat(ret.current_price * (1 - float_range), ret.current_price, 2);//
		//else
			//ret.bid_price = getRandomFloat(0, ret.current_price);//
		//ret.bid_price = parseFloat(data[6]);
		ret.asked_price = getRandomFloat(ret.current_price, ret.current_price * (1 + float_range), 2);//
		//ret.asked_price = parseFloat(data[7]);
		ret.trade_volume = parseFloat(data[10]); //
		//ret.trade_amount = parseFloat(data[9]);

		ret.buying_list_volume1 = getRandom(100, 20000);//
		ret.buying_list_price1 = ret.bid_price;


		ret.selling_list_volume1 = getRandom(100, 20000);//
		//ret.selling_list_volume1 = parseFloat(data[20]);
		ret.selling_list_price1 = ret.asked_price; //

		var times = data[3].split(' ');

		ret.update_date = times[0];
		ret.update_time = times[1];	
	}

	else if (market == 'hk'){
		ret.opening_price = parseFloat(data[2]); //
		ret.closing_price = parseFloat(data[3]); //
		ret.current_price = parseFloat(data[6]); //
		ret.highest_price = parseFloat(data[4]); //
		ret.lowest_price = parseFloat(data[5]); //

		//ret.bid_price = getRandomFloat(ret.current_price - 1, ret.current_price);//
		ret.bid_price = parseFloat(data[9]);
		//ret.asked_price = getRandomFloat(ret.current_price, ret.current_price + 1);//
		ret.asked_price = parseFloat(data[10]);
		ret.trade_volume = parseFloat(data[12]); //
		ret.trade_amount = parseFloat(data[11]); //

		ret.buying_list_volume1 = getRandom(100, 20000);//
		ret.buying_list_price1 = ret.bid_price;


		ret.selling_list_volume1 = getRandom(100, 20000);//
		//ret.selling_list_volume1 = parseFloat(data[20]);
		ret.selling_list_price1 = ret.asked_price; //

		ret.update_date = data[17];
		ret.update_time = data[18];			
	}
	return ret;
}
