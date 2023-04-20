function insert_thumbnail(thumbnail_image) {
    console.log('3');
    console.log(thumbnail_image);
    styles = document.styleSheets[0];
    // styles.removeProperty('background-image');
    // print(styles.getAttribute('background-image'));
    console.log($('#a-thumbnail').css('background-image'));
    if ($('#a-thumbnail').css('background-image') == 'none')
    {
       console.log('style does exist! removed!');
    //    styles.removeProperty('background-image');
        $('#a-thumbnail').css('background-image', '');
        // thumb.removeProperty('background-image');
        // $('#a-thumbnail').removeAttr('' )
    }


    // $('#a-thumbnail').css('background-image', `url("${thumbnail_image}")`);
    styles.insertRule(`div.blog-header { background-image: url("${thumbnail_image}"); }`);
    
    
    
}

function upload_thumbnail() {
    $(document).ready(function() {
        $("#a-thumbnail").change(function() {

            var file_data = $("#a-thumbnail").prop("files")[0];
            var form_data = new FormData();
            form_data.append("thumbnail", file_data);

            console.log('1');

            $.ajax({
                url: "upload.php",
                type: "post",
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                success: function(response) {
                    console.log('2');
                    insert_thumbnail(`../images/${file_data['name']}`);
                    $("#a-thumbnail").value = `../images/${file_data['name']}`;
                    $("#thumb").value = `../images/${file_data['name']}`;
                    console.log('4');
                }
            });
        });
    }); 
}

function upload_thumbnail_a() {
    $(document).ready(function() {
        $("#a-thumbnail").change(function() {

            var file_data = $("#a-thumbnail").prop("files")[0];
            var form_data = new FormData();
            form_data.append("a-thumbnail", file_data);

            $.ajax({
                url: "upload.php",
                type: "post",
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                success: function(response) {
                    $("#thumbnail").prop("src", `images/${file_data['name']}`);
                    $("#thumb").prop("value", `images/${file_data['name']}`);
                }
            });
        });
    }); 
}

function update_embed() {
    console.log('ran');
    var blog_title = document.getElementById("a-ytid");
    // let img = $("#thumbnail").prop("src");

    blog_title.oninput = function () {
        $("#yt-frame").prop("src", `https://www.youtube.com/embed/${blog_title.value}`);
        var img = document.getElementById("thumbnail").src;
        console.log(img);
        if (img.includes('ui/upload.svg')) {
            console.log('default image used');
            $("#thumbnail").prop("src", `https://img.youtube.com/vi/${blog_title.value}/hqdefault.jpg`);
            $("#thumb").prop("value", `https://img.youtube.com/vi/${blog_title.value}/hqdefault.jpg`);
        }

        // blog_title.src = `https://www.youtube.com/embed/${blog_title.value}`;
        console.log(`https://www.youtube.com/embed/${blog_title.value}`);
    };
}

function modify_comment() {
    $(document).ready(function() {
        $('.c-action').click(function(){
           var btnvalue = $(this).val();
           var phppath = '../includes/comment_actions.php';
           data = {'action': btnvalue};
           $.post(phppath, data, function (response) {
                alert('action performed successfully!');
           });
        });
    }); 
}