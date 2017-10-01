<?php $this->load->view('header');?>

<script type="text/javascript">
var DOMAIN = document.domain;
var WDURL = "";
var SCHEME= "<?php echo sys_skin()?>";
try{
	document.domain = '<?php echo base_url()?>';
}catch(e){
}
</script>

<style>
#matchCon { width: 200px; }
.grid-wrap{position:relative;}
.ztreeDefault{position: absolute;right: 0;top: 0;background-color: #fff;border: 1px solid #D6D5D5;width: 220px;height: 406px;overflow-y: auto;}
</style>
</head>

<body class="bgwh">
<div class="container fix p20">
	  <div class="mod-search m0 cf">
	    <div class="fl">
	      <ul class="ul-inline">
	        <li>
	          <input type="text" id="matchCon" class="ui-input ui-input-ph" value="请输入商品编号或名称或型号">
	        </li>
	        <li><a class="ui-btn mrb" id="search">查询</a><!-- <a class="ui-btn" id="refresh">刷新</a> --></li>
	      </ul>
	    </div>
        <div style="position:absolute;left: 523px;">
          <form class="ui-search" id="searchCategory" onkeydown="if(event.keyCode==13)return false;">
              <input type="text" class="ui-input" style="width:195px" id="ckey" placeholder="按名称或编码查询类别" /><button type="button" id="ssan" title="点击搜索" >搜索</button>
          </form>
        </div>
	  </div>
	  <div class="grid-wrap">
	    <table id="grid">
	    </table>
	    <div id="page"></div>
	  </div>
</div>
<script src="<?php echo base_url()?>/statics/js/dist/goodsBatch.js?2"></script>
</body>
</html>