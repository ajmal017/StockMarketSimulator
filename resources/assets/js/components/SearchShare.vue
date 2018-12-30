<template>
<div class="search-part">
	<div class="dropdown is-active">
		<div class="dropdown-trigger">
			<!--<form class="control">	-->
					<div class="field has-addons">				
					    <div class="control has-icons-left">
					      <input class="input" type="text" placeholder="查询...." v-model.trim="search_key">				      
					      <span class="icon is-small is-left">					      	
					        <i class="fa fa-search"></i>					        
					      </span>
				    	</div>
				    	<div class="control" :hidden="btnhidden">
				    		<a :href="linkto" class="button is-success">交易</a>
				    	</div>	    
					</div>
			<!--</form>-->
		</div>
		<div class="dropdown-menu" id="dropdown-menu2" role="menu">
			<div class="dropdown-content" :hidden="menuhidden" id="searchsug">				
				<suggestion
					 v-for="(suggest, stockindex) in suggestions"
					 :key="stockindex"
					 :stockindex="stockindex"
					 :code="suggest.code"
					 :name="suggest.name"
					 @setkey="setkey"						 
				></suggestion>				
			</div>
		</div>
	</div>
</div>
</template>



<script>
	//Vue.config.debug = true;
	//var thismarket = $("#market_id").text();
	
	Vue.component('suggestion', {		
		props:{
			stockindex:{
				type:String,
				required:true
			},
			name:{
				type:String,
				required:true
			},
			code:{
				required:true
			}

		},
		
		template: '<a class="dropdown-item" :id="stockindex" @click="selectkey">{{stockindex}} {{name}}</a>',

		methods:{
			selectkey: function(){
				//console.log(this.stockindex);
				var sug = {index:this.stockindex, name:this.name, code:this.code};
				this.$emit('setkey', sug);				
			}
		}
	});

	export default{
		props: [
			'thislocation'
		],

		data(){
			return {
				location: '',
				contextPath: '',
				tips: '',
				menuhidden: true,
				btnhidden:true,
				suggestions: {},
				search_key: '',
				linkto: ''				
			}
		},

		created(){			
			if(this.thislocation)
				this.location = this.thislocation;
			contextPath();			
			console.log("contextPath: " + this.contextPath);
		},

		watch:{
			search_key: function(newval){
				if (newval){
					var check = newval.split(' ');
					if(check.length == 1)
						this.getSuggestion();
					else{
						this.menuhidden = true;
					}

				}
				else{
					this.menuhidden = true;
					this.btnhidden = true;	
				}
				//this.menuhidden = true
				//this.btnhidden = true	
			}
		},

		methods:{
			getSuggestion: _.debounce(
				function(){
					var vm = this;
					this.suggestions = {};
					this.menuhidden = true;
					this.btnhidden = true;
					var keys = this.search_key.split(' ')
					var token = Math.round(new Date().getTime()/1000)
					//var url = "http://stock.yucheung.com/searchshares/" + this.location + '/' + encodeURIComponent(keys[0])
					var url = this.contextPath + "/searchshares/" + this.location + '/' + encodeURIComponent(keys[0])
					console.log('url is: ' + url)					
					axios.get(url)
						.then(function(res){
							if (typeof res.data !== undefined && res.data){								
								vm.suggestions = res.data;							
								vm.menuhidden = false;
							}
							

					})
						.catch(function(err){
							console.log('errors: ' + err);

					})
						
				},

				500
			),

			setkey: function(sug){
				if(this.location == 'china'){
					var thismarket = sug.index.substr(0,2);
					this.search_key = sug.index + ' ' + sug.name;
					this.linkto = "/showtradeform/" + thismarket + '/' + sug.code;
					this.btnhidden = false;
				}

				else if(this.location == 'us'){
					var thismarket = 'us';
					this.search_key = sug.index + ' ' + sug.name;
					this.linkto = "/showtradeform/" + thismarket + '/' + sug.code;
					this.btnhidden = false;
				}

				//this.menuhidden = true
			}
			
			
			getContextPath: function(){
				this.contextPath = window.location.pathname.substring(0, window.location.pathname.indexOf("/",2));
			}

		}

	};



</script>