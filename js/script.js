    dirs = document.getElementsByClassName('dirName');
    files = document.getElementsByClassName('fileName');
    back = document.getElementById('newDir');

    for (let i = 0; i < dirs.length; i++) {
        dirs[i].parentNode.onclick= () => {
            document.location.href+="&dir="+dirs[i].innerHTML;  
            back.onclick = () =>{
                document.location.href-="&dir="+dirs[i].innerHTML;
            }     
        }       
    }
    
    for (let i = 0; i < files.length; i++) {
        files[i].parentNode.onclick= () => {
            document.location.href+="&file="+files[i].innerHTML
            back.onclick = () =>{
                document.location.href-="&dir="+dirs[i].innerHTML;
            }
        }      
    }

    
