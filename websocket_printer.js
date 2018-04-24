var webSocket;
function doConnect(callback)
{
    webSocket = new WebSocket('ws://localhost:13528');
    //如果是https的话，端口是13529
    //socket = new WebSocket('wss://localhost:13529');
    // 打开Socket
    webSocket.onopen = function(event)
    {
        // 监听消息
        webSocket.onmessage = function(event)
        {
          var response = JSON.parse(event.data);
          console.log(response);
          if (response.cmd == 'getPrinters') {
              getPrintersHandler(response);//处理打印机列表
          } else if (response.cmd == 'printerConfig') {
             // printConfigHandler(response);
          } 
            
        };
        // 监听webSocket的关闭
        webSocket.onclose = function(event)
        {
            console.log('Client notified socket has closed',event);
        };

        callback();
    };
}
/***
 * 
 * 获取请求的UUID，指定长度和进制,如 
 * getUUID(8, 2)   //"01001010" 8 character (base=2)
 * getUUID(8, 10) // "47473046" 8 character ID (base=10)
 * getUUID(8, 16) // "098F4D35"。 8 character ID (base=16)
 *   
 */
function getUUID(len, radix) {
    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
    var uuid = [], i;
    radix = radix || chars.length; 
    if (len) {
      for (i = 0; i < len; i++) uuid[i] = chars[0 | Math.random()*radix];
    } else {
      var r;
      uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
      uuid[14] = '4';
      for (i = 0; i < 36; i++) {
        if (!uuid[i]) {
          r = 0 | Math.random()*16;
          uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
        }
      }
    }
    return uuid.join('');
}
/***
 * 构造request对象
 */
function getRequestObject(cmd) {
    var request  = new Object();
    request.requestID=getUUID(8, 16);
    request.version="1.0";
    request.cmd=cmd;
    return request;
}
/***
 * 获取自定义区数据以及模板URL
 * waybillNO 电子面单号
 */
function getCustomAreaData(waybillNO){
    //获取waybill对应的自定义区的JSON object，此处的ajaxGet函数是伪代码
    var jsonObject = ajaxGet(waybillNO);
    var ret = new Object();
    ret.templateURL=jsonObject.content.templateURL;
    ret.data=jsonObject.content.data;
    return ret;
}
/***
 * 获取电子面单Json 数据
 * waybillNO 电子面单号
 */
function getWaybillJson(waybillNO){
    //获取waybill对应的json object，此处的ajaxGet函数是伪代码
    var jsonObject = ajaxGet(waybillNO);
    return jsonObject;
}

function ajaxGet()
{
  var print_data; 
  $.ajaxSetup({    
    async : false    
  });  
  $.get('/getdata.php', function(data) {
      print_data = data;
    },'json');
  return print_data;
}

/**
 * 请求打印机列表demo
 * */
function getPrinter()
{
  var request  = getRequestObject("getPrinters");
  webSocket.send(JSON.stringify(request));
}


/**
 * 弹窗模式配置打印机
 * */
/*var request  = getRequestObject("printerConfig");
webSocket.send(JSON.stringify(request));*/
/**
 * 打印电子面单
 * printer 指定要使用那台打印机
 * waybillArray 要打印的电子面单的数组
 */
function doPrint(printer, waybillArray)
{
    var request = getRequestObject("print");    
    request.task = new Object();
    request.task.taskID = getUUID(8,10);
    request.task.preview = false;
    request.task.printer = printer;
    //request.task.previewType = 'image';
    var documents = new Array();
    for(i=0;i<waybillArray.length;i++) {
         var doc = new Object();
         doc.documentID = waybillArray[i];
         var content = new Array();
         var waybillJson = getWaybillJson(waybillArray[i]);
         //var customAreaData = getCustomAreaData(waybillArray[i]);
         //content.push(waybillJson,customAreaData);
         content.push(waybillJson);
         doc.contents = content;
         documents.push(doc);
    }
    request.task.documents = documents;
    console.log(request);
    webSocket.send(JSON.stringify(request));
}

function getPrintersHandler($respone)
{
  console.log($respone);
}
/**
 * 响应请求demo
 * */
/*websocket.onmessage = function (event) {   
    var response = eval(event.data);
    if (response.cmd == 'getPrinters') {
        getPrintersHandler(response);//处理打印机列表
    } else if (response.cmd == 'printerConfig') {
        printConfigHandler(response);
    } 
};*/