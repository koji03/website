<template>
      <!-- 画像投稿 -->
    <div class="p-scheduled__form__tweet">
        <div>
           {{currentCount}}/{{fileCount}} {{ message.gif }}
        </div>
        <input
        @change="inputFileList($event)"
        multiple="multiple"
        accept="image/*"
        type="file"
        v-if="fileDelete"
        class="u-margin__shceduled__input__file p-scheduled__form__file"
        enctype="multipart/form-data" files=true
        >
        <div class="p-scheduled__images">
            <div class="p-scheduled__images__item"
            v-for="(uploadFile, index) in list"
            :key="index"
            >
            <img :src="uploadFile.url" @click="deletePhoto(index)" class="p-scheduled__images__item__image">
            </div>
        </div>
        <div class="c-error u-margin--error--scheduled">{{error}}</div>      
    </div>
</template>
<script>
import { mapState } from 'vuex'
export default {
  data() {
        return{
           error:'',
           fileDelete:true,
           list:[],
           fileCount:4,
           currentCount:0,
           message:{
               confirm:'写真を削除してもいですか？',
               photo:'画像は４つ以上選択できません。',
               gif:'gifは投稿できません。'
           }
        }
    },
    methods: {
        //削除
        deletePhoto(uploadFile){
            if(confirm(this.message.confirm)){
                this.$store.commit('modal/deleteUploadFileList',uploadFile)
            }
        },
        //拡張子を取得
        getExt(filename){
            var pos = filename.lastIndexOf('.');
            if (pos === -1) return '';
            var type = filename.slice(pos + 1)
            type = type.toUpperCase()
            return type;
        },
        //保存する写真をmodal.jsに格納する。
        inputFileList(event)  {
            /**
             * fileDeleteはv-ifで使用しています。
             * 再描画させることで選択の解除をしています。
             */
            this.error = ''
            this.$store.commit('modal/setErrorFlag',false)
            this.fileDelete = false
            this.$nextTick(function () {
            this.fileDelete = true
            })
            if(this.updatedFileList.length >=4){
                this.error = this.message.photo
                this.$store.commit('modal/setErrorFlag',true)
                return false
            }
            const fileList = event.target.files[0]
            if (fileList === 0) {
                return
            }
            let typeList=[]
            this.updatedFileList.forEach(element => {
                typeList.push(this.getExt((element.name)? element.name : element.filename))
            });
            typeList.push(this.getExt(fileList.name))
            let count =typeList.filter(function(x){return x==='GIF'}).length;
            if(count >=1){
                this.$store.commit('modal/setErrorFlag',true)
                this.error = this.message.gif
                return false
            }
            this.$store.commit('modal/setUploadFileList',fileList)
        },
        /**
         * 
         * createdで使用。
         * listに画像を格納する。
         * その時にBlobだとフォームから投稿された画像なので
         * FileReaderでURLを取得してそれを格納
         * そうでない場合はawsS3に保存した画像で、取得した際にURLで取得しているのでそのまま格納
         * 
         * 
         */
        async fileList(val){
            // FileAPIは、APIなので複数ファイルを扱う時は、
            // for文の中でawaitする必要があります
            this.list = []
                for (var file of val) {
                    if(file instanceof Blob){
                        const fileData = {
                            url: ''
                        }
                        const fileReader = new FileReader()
                        await (async () => {
                        fileReader.onload = async () => {
                            // ファイルURL取得
                            fileData.url = fileReader.result
                        }
                        // FileAPIの起動
                        fileReader.readAsDataURL(file)
                        })()
                        this.list.push(fileData)
                    }else{
                    this.list.push(file)
                }
            }
           
        }
    },
    created:function(){
        this.fileList(this.$store.state.modal.uploadFileList)
    },
    computed:{
        ...mapState({
            updatedFileList: state => state.modal.uploadFileList
        }),
    },
    watch:{
        updatedFileList:async function(val){
            this.fileList(val)
        },
        list:function(){
            this.currentCount = this.list.length
        }
    }
    
}
</script>