<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Attendance Information System</title>

        <link rel="stylesheet" type="text/css" href="<?= base_url()."files/css/style.css"; ?>">
            
        <!-- production -->
        <script type="text/javascript" src="<?= base_url('thirdparty/plupload') ?>/js/plupload.full.min.js"></script>


        <!-- debug 
        <script type="text/javascript" src="<?= base_url('thirdparty/plupload') ?>/js/moxie.js"></script>
        <script type="text/javascript" src="<?= base_url('thirdparty/plupload') ?>/js/plupload.dev.js"></script>
        -->

        <script src="<?= base_url()."files/js/jquery.min.js"; ?>"></script>
        <script>
            function import_mdb_all() {
                $.ajax({
                    type: "POST",
                    data: {mdbfilepath:$('#mdbfile').val()},
                    url: "<?= site_url("/import/mdb_setting/"); ?>",
                    error: function () {
                        $('#loading_checkinout').html('Error Do Setting'); 
                    },
                    success: function () {
                        $('#loading_checkinout').html('<?php echo 'Import Attendance Data <img src="' . base_url() . 'files/image/ajax-loader.gif">'; ?>');
                        $.ajax({
                            type: "POST",
                            data: "MDB",
                            url: "<?= site_url("/import/mdb_checkinout"); ?>",
                            error: function () {
                                $('#loading_checkinout').html('Error Import Attendance Data'); 
                            },
                            success: function () {
                                $('#loading_checkinout').html('Attendance Data Imported');
                                $('#loading_userinfo').html('<?php echo 'Import User Data <img src="' . base_url() . 'files/image/ajax-loader.gif">'; ?>');
                                $.ajax({
                                    type: "POST",
                                    data: "MDB",
                                    url: "<?= site_url("/import/mdb_userinfo"); ?>",
                                    error: function () {
                                        $('#loading_userinfo').html('Error Import User Data'); 
                                    },
                                    success: function() {
                                        $('#loading_userinfo').html('User Data Imported');
                                        $('#loading_departments').html('<?php echo 'Import Department Data <img src="' . base_url() . 'files/image/ajax-loader.gif">'; ?>');
                                        $.ajax({
                                            type: "POST",
                                            data: "MDB",
                                            url: "<?= site_url("/import/mdb_departments"); ?>",
                                            error: function () {
                                                $('#loading_departments').html('Error Import Department Data'); 
                                            },
                                            success: function() {
                                                $.ajax({
                                                    type: "POST",
                                                    data: "MDB",
                                                    url: "<?= site_url("/import/clean_directory"); ?>",
                                                    error: function () {
                                                        $('#process_data').html('Error Cleaning Directory'); 
                                                    },
                                                    success: function() {
                                                        $('#loading_departments').html('Department Data Imported');
                                                        $('#process_data').html('<?php echo 'Processing Data <img src="' . base_url() . 'files/image/ajax-loader.gif">'; ?>');
                                                        $.ajax({
                                                            type: "POST",
                                                            data: "MDB",
                                                            url: "<?= site_url("/import/mdb_process"); ?>",
                                                            error: function () {
                                                                $('#process_data').html('Error Processing Data'); 
                                                            },
                                                            success: function() {
                                                                $('#process_data').html('Importing Data Succesfull');
                                                            }
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
             }
        </script>
    </head>
    <body>

        <div id="container">
            <h1>Attendance Information System</h1>

            <div id="body">
                <h3>IMPORT MDB</h3>
                <!--<p><input type="text" name="mdbfile" id="mdbfile" value="<?= $mdbfilepath_local ?>" DISABLED style="width: 400px;"/></p>
                <p><input type="button" name="import" value="Import MDB" onclick="buttonClick()" /></p>
                -->
                <div id="containerfiles" style="border-style:none; margin-left: 0px; ">
                    <a id="pickfiles" href="javascript:;">[Select files]</a> 
                    <a id="processfiles" href="javascript:;">[Process files]</a>
                </div>
                <div id="filelist" style="border-style:none;">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
                
                <div id="loading_checkinout"></div>
                <div id="loading_userinfo"></div>
                <div id="loading_departments"></div>
                <div id="process_data"></div>
                <p><a href="<?= site_url("menu"); ?>">Kembali</a></p>
            </div>
            <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
        </div>

        <script type="text/javascript">
        // Custom example logic

        var uploader = new plupload.Uploader({
                runtimes : 'html5,flash,silverlight,html4',
                browse_button : 'pickfiles', // you can pass in id...
                container: document.getElementById('containerfiles'), // ... or DOM Element itself
                url : '<?= base_url('thirdparty/plupload/examples') ?>/upload.php',
                flash_swf_url : '<?= base_url('thirdparty/plupload') ?>/js/Moxie.swf',
                silverlight_xap_url : '<?= base_url('thirdparty/plupload') ?>/js/Moxie.xap',
	
                //filters : {
                //        max_file_size : '1024mb',
                //        mime_types: [
                //                {title : "Mdb Files", extensions : "mdb"}
                //        ]
                //},
                        
                filters : [{title : "Mdb Files", extensions : "mdb"}],
	
                // Rename files by clicking on their titles
                rename: true,
                
                max_file_count: 1,
	
                chunk_size: '<?= strtolower(ini_get('upload_max_filesize')); ?>b',
        
                multi_selection: false,
        
                init: {
                        PostInit: function() {
                                document.getElementById('filelist').innerHTML = '';

                                document.getElementById('processfiles').onclick = function() {
                                        uploader.start();
                                        $("#containerfiles").toggle();
                                        return false;
                                };
                        },

                        FilesAdded: function(up, files) {
                                plupload.each(files, function(file) {
                                        document.getElementById('filelist').innerHTML += '<div id="' + file.id + '"> File ' + file.name + ' (' + plupload.formatSize(file.size) + ') selected <br/><span></span></div>';
                                });
                        },

                        UploadProgress: function(up, file) {
                                document.getElementById(file.id).getElementsByTagName('span')[0].innerHTML = 'Uploading File [' + file.percent + "%]";
                        },

                        Error: function(up, err) {
                                document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
                        }
                }
        
        
        });

        uploader.init();
        
        uploader.bind('FilesAdded', function(up) {
            if ( up.files.length > 1 && uploader.state != 2) {
                up.removeFile(up.files[0]);
                up.refresh();
                document.getElementById('filelist').innerHTML = '';
            }
        });

        uploader.bind('UploadProgress', function(up, file) {
            if (file.percent == 100) {
                document.getElementById(file.id).getElementsByTagName('span')[0].innerHTML = 'File Uploaded';
                import_mdb_all();
            }
        });
        
        $.ajax({
            type: "POST",
            data: "MDB",
            url: "<?= site_url("/import/clean_directory"); ?>"
        });

        </script>
        <pre id="console" style="border-style:none;"></pre>
    </body>
</html>