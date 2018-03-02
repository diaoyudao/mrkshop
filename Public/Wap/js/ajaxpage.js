var AjaxPage = new Object;
AjaxPage.pageCount = 1;//总页码
AjaxPage.append = 0;//是否在后面加载
AjaxPage.dataType = "json";//返回数据类型  html
AjaxPage.method = 'GET';
AjaxPage.canpage=true;
AjaxPage.Conten = $('#listDiv');
AjaxPage.alert=$('#loding_ajax');
AjaxPage.filter = new Object;
AjaxPage.filter.page=1;
AjaxPage.url = location.href.lastIndexOf("?") == -1 ? location.href.substring((location.href.lastIndexOf("/")) + 1) : location.href.substring((location.href.lastIndexOf("/")) + 1, location.href.lastIndexOf("?"));
/**
 * 翻页
 */
AjaxPage.gotoPage = function(page)
{
  if (page != null) this.filter.page = page;

  if (this.filter.page > this.pageCount) this.filter.page = this.pageCount;

  this.filter.page_size = this.getPageSize();

  this.loadList();
}

/**
 * 载入列表
 */
AjaxPage.loadList = function()
{
  this.loadList_beafor();
  $.ajax({
	  method: AjaxPage.method,
	  url: this.url,
	  data: this.filter,
	  dataType: this.dataType
	}).done(function( result ) {
		AjaxPage.canpage=true;
		if(AjaxPage.dataType=='html'){
		      	  if(AjaxPage.append){
			      	AjaxPage.Conten.append(result);
			      }else{
			    	  AjaxPage.Conten.html(result);
			      }
		}else{
		  if (result.error > 0)
		  {
		    alert(result.message);
		  }
		  else
		  {
		    try
		    {
		      if(AjaxPage.append){
		      	AjaxPage.Conten.append(result.content);
		      }else{
		    	  AjaxPage.Conten.html(result.content);
		      }
		      if (typeof result.filter == "object")
		      {
		        AjaxPage.filter = result.filter;
		      }

		      AjaxPage.pageCount = result.page_count;
		    }
		    catch (e)
		    {
		      alert(e.message);
		    }
		  }
		}
		AjaxPage.loadList_after();
	});
}
/**
 * 第一页
 */
AjaxPage.gotoPageFirst = function()
{
  if (this.filter.page > 1)
  {
	  AjaxPage.gotoPage(1);
  }
}
/**
 * 上一页
 */
AjaxPage.gotoPagePrev = function()
{
  if (this.filter.page > 1)
  {
	  AjaxPage.gotoPage(this.filter.page - 1);
  }
}
/**
 * 下一页
 */
AjaxPage.gotoPageNext = function()
{
  if (this.filter.page < AjaxPage.pageCount)
  {
	  AjaxPage.gotoPage(parseInt(this.filter.page) + 1);
  }
}
/**
 * 最后一页
 */
AjaxPage.gotoPageLast = function()
{
  if (this.filter.page < AjaxPage.pageCount)
  {
	  AjaxPage.gotoPage(AjaxPage.pageCount);
  }
}
/**
 * 修改每页显示数量
 */
AjaxPage.changePageSize = function(e)
{
    var evt = (typeof e == "undefined") ? window.event : e;
    if (evt.keyCode == 13)
    {
        AjaxPage.gotoPage();
        return false;
    };
}
/**
 * 组合页面参数
 */
/*AjaxPage.compileFilter = function()
{
  var args = new Array();
  for (var i in this.filter)
  {
    if (typeof(this.filter[i]) != "function" && typeof(this.filter[i]) != "undefined")
    {
    	args[i]=this.filter[i];//args += "&" + i + "=" + encodeURIComponent(this.filter[i]);
    }
  }

  return args;
}*/
/**
 * 修改每页显示数量
 */
AjaxPage.getPageSize = function()
{
  var ps = 15;

  pageSize = $("#pageSize").val();

  if (pageSize)
  {
    ps = /\D+/.test(pageSize) ? pageSize : 15;
    document.cookie = "CMCP[page_size]=" + ps + ";";
  }
}

AjaxPage.loadList_beafor=function(){
	this.alert.show().text('加载中····');
}
AjaxPage.loadList_after=function(){
	if(this.filter['page'] == this.pageCount)
	{
		this.alert.show().text("已全部加载");
	}else{
		this.alert.show().text("向上滑动继续加载");
	}

}