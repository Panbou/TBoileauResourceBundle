$(function () {
    $.getScript("https://cdnjs.cloudflare.com/ajax/libs/cropper/3.1.1/cropper.min.js");
    $('<link/>', {
        rel: 'stylesheet',
        type: 'text/css',
        href: 'https://cdnjs.cloudflare.com/ajax/libs/cropper/3.1.1/cropper.min.css'
    }).appendTo('head');
    $("body").on("change", "._resource", function () {
        var dir = $(this).data("dir");
        var maxSize = $(this).data("maxsize");
        var minHeight = $(this).data("minheight");
        var maxHeight = $(this).data("maxheight");
        var minWidth = $(this).data("minwidth");
        var maxWidth = $(this).data("maxwidth");
        var ratio = $(this).data("ratio");
        var types = $(this).data("types").split(",");
        var $input = $($(this).data("rel"));
        var $validation = $($(this).data("validation"));
        var $progressBar = $($(this).data("progressbar"));
        var $modal = $($(this).data("modal"));
        var file = this.files[0];
        var errors = [];
        if (maxSize && file.size > maxSize) {
            errors.push("La taille de votre fichier est trop grosse.");
        }
        if (types.indexOf(file.type) == -1) {
            errors.push("Ce type fichier n'est pas autorisé.");
        }
        if (errors.length > 0) {
            $validation.html(errors.join("<br/>"));
            $(this).addClass("is-invalid");
        } else {
            $validation.html("");
            $(this).addClass("is-valid");
        }
        console.log()
        var isImage =
            types.indexOf("image/gif") + types.indexOf("image/jpeg") + types.indexOf("image/png") > 0 &&
            (
                typeof minHeight != "undefined" ||
                typeof maxHeight != "undefined" ||
                typeof minWidth != "undefined" ||
                typeof maxWidth != "undefined"
            );
        console.log(minHeight,maxHeight ,minWidth,maxWidth)
        var formData = new FormData();
        formData.append("file", file);
        formData.append("dir", dir);
        var $self = $(this);
        $.ajax({
            // Your server script to process the upload
            url: '/app_dev.php/_resource/upload',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    $progressBar.parent(".progress").removeClass("d-none");
                    myXhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            var valueNow = Math.ceil(e.loaded / e.total) * 100;
                            $progressBar.attr("aria-valuenow", valueNow).css("width", valueNow + "%");
                        }
                    }, false);
                }
                return myXhr;
            },
            success: function (response) {
                $progressBar.parent(".progress").addClass("d-none");
                if (response.error) {
                    $validation.html(response.message);
                    $self.addClass("is-invalid");
                }else if(isImage){
                    $self.addClass("is-valid");
                    $modal.on("shown.bs.modal", function() {
                        var $body = $modal.find(".modal-body");
                        $body.html('<img src="/'+response.resource+'" width="100%" style="max-width:100%;">');

                        $body.find("img").cropper({
                            zoomOnWheel: false,
                            aspectRatio: parseFloat(ratio),
                            crop: function(e) {
                                $self.attr("data-file", response.resource);
                                $self.attr("data-x",e.x);
                                $self.attr("data-y", e.y);
                                $self.attr("data-width", e.width);
                                $self.attr("data-height", e.height);
                            }
                        });
                        $modal.find("button").on("click", function() {
                            $.ajax({
                                url: "/app_dev.php/_resource/crop",
                                type: "post",
                                data: $self.data(),
                                beforeSend: function() {

                                },
                                success: function(response) {
                                    if(!response.error) {
                                        $modal.modal("hide");
                                        $input.val(response.resource);
                                    }else{
                                        alert(response.message);
                                    }
                                }
                            })
                        })
                    }).modal("show");
                }else{
                    $input.val(response.resource);
                }
            }
        });

    })
});
