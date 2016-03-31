// JavaScript Document
function sendFileToServer(uploadUrl,formData,status,cnt)
{
	$("#statusbar_"+cnt).append('<span class="message">File Uploading</span>');
    var uploadURL =uploadUrl; //Upload URL
    var extraData ={}; //Extra Data.
    var jqXHR=$.ajax({
            xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        //Set progress
                        status.setProgress(percent);
                    }, false);
                }
            return xhrobj;
        },
    url: uploadURL,
    type: "POST",
    contentType:false,
    processData: false,
        cache: false,
        data: formData,
        success: function(data){
            status.setProgress(100);
			data = JSON.parse(data);
            $("#statusbar_"+cnt+" .message").html(data.msg);        
        },
		error:function(data)
		{
			status.setProgress(100);
			data = JSON.parse(data);
			if(data.status)
			{
				$("#statusbar_"+cnt+" .message").attr('style','color:red;');		
			}
			$("#statusbar_"+cnt+" .message").html(data);    
		},
		complete: function(msg)
		{
		}
    });
 
    //status.setAbort(jqXHR);
}
 
var rowCount=0;
function createStatusbar(obj)
{
     rowCount++;
     var row="odd";
     if(rowCount %2 ==0) row ="even";
     this.statusbar = $('<span class="statusbar '+row+'" id="statusbar_'+rowCount+'"></span>');
     this.filename = $('<span class="filename"></span>').appendTo(this.statusbar);
     this.size = $('<span class="filesize"></span>').appendTo(this.statusbar);
     this.progressBar = $('<span class="progressBar"><span></span></span>').appendTo(this.statusbar);
     this.abort = $('<span class="abort" style="display:none;">Bruk PDF eller bildefil</span>').appendTo(this.statusbar);
	 this.abort.hide();
     obj.after(this.statusbar);
 
    this.setFileNameSize = function(name,size)
    {
        var sizeStr="";
        var sizeKB = size/1024;
        if(parseInt(sizeKB) > 1024)
        {
            var sizeMB = sizeKB/1024;
            sizeStr = sizeMB.toFixed(2)+" MB";
        }
        else
        {
            sizeStr = sizeKB.toFixed(2)+" KB";
        }
 
        this.filename.html(name);
        this.size.html(sizeStr);
    }
    this.setFileNameSize1 = function(name,size)
    {
        var sizeStr="";
        var sizeKB = size/1024;
        if(parseInt(sizeKB) > 1024)
        {
            var sizeMB = sizeKB/1024;
            sizeStr = sizeMB.toFixed(2)+" MB";
        }
        else
        {
            sizeStr = sizeKB.toFixed(2)+" KB";
        }
         sizeStr= sizeStr+" Ble ikke lagret";
        this.filename.html(name);
        this.size.html(sizeStr);
		this.abort.show();
    }	
    this.setProgress = function(progress)
    {      
        var progressBarWidth =progress*this.progressBar.width()/ 100; 
        this.progressBar.find('span').animate({ width: progressBarWidth }, 10).html(progress + "% ");
        if(parseInt(progress) >= 100)
        {
            this.abort.hide();
        }
    }
    this.setAbort = function(jqxhr)
    {
        var sb = this.statusbar;
        this.abort.click(function()
        {
            jqxhr.abort();
            sb.hide();
        });
    }
}
function handleFileUpload(uploadUrl,files,obj,button_type, pasientId)
{
  if(uploadUrl)
  {
   var counter = $('#counterStatus').val();
   for (var i = 0; i < files.length; i++)
   {
        var fd = new FormData();
        fd.append('file', files[i]);
		fd.append('button_type', button_type);
		fd.append('patient_id', pasientId);
 
        var status = new createStatusbar(obj); //Using this we can set progress.
        
		//alert(files[i].name);
		if(TestFileType(files[i].name, ['.gif', '.jpg', '.JPG', '.png', '.jpeg','.phtml','.pdf','.doc','.docx'])== 0)
		{
			//alert("File format not valid try again ");
			status.setFileNameSize1(files[i].name,files[i].size);
		}
		else{
			status.setFileNameSize(files[i].name,files[i].size);
			
			sendFileToServer(uploadUrl,fd,status,counter);
		}
 		counter++;
		$('#counterStatus').val(counter);
   }
  }
  else
  {
	  alert('Problem While Uploading File');
  }
}


function TestFileType( fileName, fileTypes ) {
	if (!fileName) return;
	dots = fileName.split(".")
	//get the part AFTER the LAST period.
	fileType = "." + dots[dots.length-1];
	if(fileTypes.join().indexOf(fileType) != -1){
		//alert('That file is OK!')
		return 1;
	}
	else{
		//alert("Please only upload files of types: \n\n" + (fileTypes.join(" .")) + "\n\nPlease select a new file and try again.");
		return 0;
	}
	/*return (fileTypes.join(".").indexOf(fileType) != -1) ?
	alert('That file is OK!') :
	alert("Please only upload files that end in types: \n\n" + (fileTypes.join(" .")) + "\n\nPlease select a new file and try again.");*/
}
