
    function alert(text,title='Alert message'){
        $.gritter.add({
			title: title,
			text: text,
			sticky: false,
            time: 2000,
		});

    }

    async function axiosGet(url){
        axios.get(url).then(resp => {
            console.log(resp.data);
        });
        
    }
    async function axiosPost(payload,url,callback = undefined){
        await axios.post(url,payload).then(resp => {
    if(callback)
    {

        callback(isJson(resp.data));
    }
});

    }


    function isJson(str) {
    
        try {
            str = JSON.parse(str);
        } catch (e) {
            return str;
        }
        return str;
    }
