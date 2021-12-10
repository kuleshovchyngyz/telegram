$('#telegramBot div').click(function () {
    let id = $(this).data('id');
   console.log(id);
    response('get',`/bot/${id}`,'null').then(data => {
        console.log(data);
        window.location.href = '/';
    });
});


async function response(type, url, data){
    const result = await $.ajax({
        url: url,
        type: type,
        data: data
    })
    return result;
}
