/**
 * https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/
 */
(function CodersRepository( ){
    
    var _repo = {
        'collection':'default',
        //'URL': typeof URL !== 'undefined' ? URL : null,
        'timeout': 2000,
        'inputs':{
            'dropzone':'coders-repo-dropzone',
            'uploader':'coders-repo-uploader'
        },
        'options':{
            'fileSize': 256 * 256 * 256
        }
    };
    /**
     * @returns {String}
     */
    this.url = function( vars ){
        
        var url = window.location.href;
        
        if( typeof vars === 'object' ){

            var params = [];
            
            Object.keys( vars ).forEach(function(item){
            
                params.push( item + '=' + vars[ item ] );
            });
            
            return url +
                ( url.indexOf('?') > -1 ? '&' : '?' ) +
                params.join('&');
        }
        
        //return _repo.URL + '?page=coders-repository';
        return url;
    };
    /**
     * @returns {HTMLDivElement}
     */
    this.getDropZone = function(){
        
        var dropZone = document.getElementById( _repo.inputs.dropzone );
        
        return dropZone !== null ? dropZone : this.appendUploader();
    };
    /**
     * @returns {Element}
     */
    this.getUploadButton = function(){
        
        return document.getElementsByClassName( _repo.inputs.uploader );
    };
    /**
     * @returns {CodersRepository}
     */
    this.server = function(){
        
        return this;
    };
    /**
     * @returns {CodersRepository}
     */
    this.notify = function( message ){
        
        return this;
    };
    /**
     * @returns {CodersRepository}
     */
    this.createCollection = function( collection ){
        
        return this;
    };
    /**
     * @param {Array | FileList} files 
     * @returns {CodersRepository}
     */
    this.uploadFiles = function( files ){
        
        if( files && files.length ){
            
            console.log( 'Sending data to ' + this.url() + ' ...' );
            //console.log( files );
            
            var _controller = this;
            
            Array.prototype.forEach.call( files , function( upload ){

                var formData = {
                    'upload': upload
                };
                
                //console.log( 'Uploading ' + JSON.stringify( upload.toString( ) ) + ' ...' );
                
                var url = _controller.url({'task':'dragDrop'});
                //console.log( url );
                fetch( url , { method: 'POST', body: formData } )
                    .then( (response) => response.json( ) )
                    .then(function(data){
                        _controller.getDropZone().classList.add('completed');
                        console.log(data);
                        window.setTimeout(function(){
                            _controller.getDropZone().classList.remove('completed');
                        }, _repo.timeout );
                    }).catch( function( error ){
                        _controller.getDropZone().classList.add('error');
                        console.log(error);
                        window.setTimeout(function(){
                            _controller.getDropZone().classList.remove('error');
                        }, _repo.timeout );
                    });
            });
        }
        
        return this;
    };
    /**
     * @param {String} collection
     * @returns {CodersRepository}
     */
    this.loadCollection = function( collection ){
        
        return this;
    };
    /**
     * @returns {Array}
     */
    this.acceptedTypes = function(){
        return [
            'image/png',
            'image/gif',
            'image/jpeg',
            'image/bmp',
            'text/plain',
            'text/html',
            'text/json',
            'application/json'
        ];
    };
    /**
     * @param {String} input
     * @returns {String}
     */
    this.cleanFileName = function( input ){

        var filename = input.split('\\');
        
        return filename[ filename.length - 1 ];
    };
    /**
     * @returns {HTMLDivElement}
     */
    this.appendUploader = function( ){
        
        var _controller = this;
       
        var inputFile = document.createElement('input');
        inputFile.type = 'file';
        inputFile.name = 'upload';
        inputFile.multiple = true;
        inputFile.id = _repo.inputs.dropzone + '_input';
        //inputFile.accept = this.acceptedTypes().join(', ');
        inputFile.addEventListener( 'click', e => {
            return true;
        });
        var inputSize = document.createElement('input');
        inputSize.type = 'hidden';
        inputSize.name = 'MAX_FILE_SIZE';
        inputSize.value = _repo.options.fileSize;
        var inputLabel = document.createElement('label');
        inputLabel.setAttribute('for' , inputFile.id );
        inputLabel.className = 'caption';
        inputLabel.innerHTML = 'Select or drop your files here';
        inputLabel.addEventListener( 'click', e => {
            return true;
        });
        var pBarContainer = document.createElement('div');
        pBarContainer.className = 'progress-bar';
        var pBarProgress = document.createElement('span');
        pBarProgress.className = 'progress-status';
        pBarContainer.appendChild(pBarProgress);
        var btnUpload = document.createElement('button');
        btnUpload.type = 'submit';
        btnUpload.name = 'task';
        btnUpload.value = 'upload';
        btnUpload.className = 'btn btn-big';
        btnUpload.innerHTML = 'Upload';
        var formData = document.createElement('form');
        formData.method = 'POST';
        formData.action = this.url({'task':'upload'});
        formData.enctype = 'multipart/form-data';
        formData.appendChild(btnUpload);
        formData.appendChild(inputSize);
        formData.appendChild(inputFile).addEventListener( 'change',function(e){
            e.preventDefault();
            btnUpload.innerHTML = _controller.cleanFileName( this.value.toString( ) );
            console.log( btnUpload.innerHTML );
            return true;
        });
        var dropZone = document.createElement('div');
        dropZone.className = 'coders-repo drop-zone container';
        dropZone.appendChild(inputLabel);
        dropZone.appendChild(pBarContainer);
        dropZone.appendChild(formData);
        dropZone.addEventListener( 'click', e => {
            //e.preventDefault();
            e.stopPropagation();
            return false;
        });
        
        ['dragenter','dragleave','dragover','drop'].forEach( function( event ){
            dropZone.addEventListener(event, function(e){
                e.preventDefault();
                e.stopPropagation();
                switch( event ){
                    case 'dragenter':
                    case 'dragover':
                        dropZone.classList.add('highlight');
                        break;
                    case 'dragleave':
                        dropZone.classList.remove('highlight');
                        break;
                    case 'drop':
                        dropZone.classList.remove('uploading');
                        _controller.uploadFiles( e.dataTransfer.files );
                        break;
                }
            }, false);
        });
        
        var dropModal = document.createElement('div');
        dropModal.className = 'coders-repo drop-zone';
        dropModal.id = _repo.inputs.dropzone;
        dropModal.addEventListener( 'click', function(e){
            e.preventDefault();
            this.classList.remove('show');
            return true;
        });
        dropModal.appendChild(dropZone);
        return document.body.appendChild( dropModal );
    };
    /**
     * @returns {CodersRepository}
     */
    this.bind = function(){

        var _controller = this;

        document.addEventListener('DOMContentLoaded',function(e){

            var uploadButton = _controller.getUploadButton();

            if( uploadButton !== null ){

                Array.prototype.forEach.call( uploadButton , function( btn ){

                    btn.addEventListener( 'click' , function(e){
                        e.preventDefault();
                        console.log('Opening uploader!!');
                        _controller.getDropZone().classList.add('show');
                        return true;
                    });
                });
            }
        });

        return this;
    };
    
    return this.bind();
})( /* autosetup */ );


