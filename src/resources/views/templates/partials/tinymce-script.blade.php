    <script src="//cloud.tinymce.com/stable/tinymce.min.js?apiKey={{ $tinymce_key }}"></script>

    <script type="text/javascript">
        tinymce.init({
          selector: '#descripcion_larga',
          height: 250,
          plugins: [
            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons paste textcolor colorpicker textpattern codesample toc'
          ],
          toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons | codesample',
          imagetools_cors_hosts: ['www.tinymce.com', 'codepen.io'],
          // enable title field in the Image dialog
          image_title: true, 
          // enable automatic uploads of images represented by blob or data URIs
          automatic_uploads: false,
          // URL of our upload handler (for more details check: https://www.tinymce.com/docs/configure/file-image-upload/#images_upload_url)
          //images_upload_url: "{{route('templates.upload')}}",
          // here we add custom filepicker only to Image dialog
          file_picker_types: 'image',
          // relative url false
          relative_urls : false,
          //host url active
          remove_script_host : false,
          // and here's our custom image picker
          file_picker_callback: function(cb, value, meta) {

            if (meta.filetype == 'image') {
                inputImage.onchange = function() {
                  var file = this.files[0];
                  var reader = new FileReader();
                  reader.readAsDataURL(file);
                  reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    var fd = new FormData($('#formimage')[0]);
                    $.ajax({
                        url: "{{route('templates.upload')}}",
                        data: fd,
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(data){console.log(data);
                            cb(data, { title: file.name });
                        }
                    });                    
                  };                  
                };
                inputImage.click();                
            }

          },
          setup:function(editor) {
               editor.on('change', function(e) {
                   $('#descripcion_larga').text(editor.getContent());
                   $('#descripcion_larga').val(editor.getContent());
               });
           }
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
              $("body").append("<form method='post' enctype='multipart/form-data' action='{{route('templates.upload')}}' class='hide' id='formimage' style='display:none'></form>");
              $("#formimage")
                .append('<input id="fileimage" name="fileimage" type="file" accept="image/*">')
                .append('{{ csrf_field() }}');
              inputImage = document.getElementById("fileimage");

              $("body").append("<form method='post' enctype='multipart/form-data' action='{{route('templates.upload')}}' class='hide' id='formfile' style='display:none'></form>");
              $("#formfile").append('<input id="filefile" name="filefile" type="file">');
              inputFile = document.getElementById("filefile");

        })
    </script>