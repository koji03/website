<template>
    <div>
        <!-- ツイートを予約する機能があります。 -->
        <Loading v-if="load"/>
        <div class="p-scheduled__wrap u-margin__scheduled__wrap" v-if="!load">
            <scheduledDateForm/>
            <scheduledTimeForm/>
            <div class="c-error">{{error}}</div>
            <scheduledFormText/>
        </div>
        <div class="c-modal__btn" v-if="!load">
            <span @click="scheduled" v-if="!scheduledTweetsId" class="c-modal__btn__item">
                予約する
            </span>
            <span v-if="scheduledTweetsId" @click="scheduled" class="c-modal__btn__item">
                更新する
            </span>
            <span v-if="scheduledTweetsId" @click="deleteScheduled" class="c-modal__btn__item">
                削除する
            </span>
        </div>
        <Error class="u-margin--error"
        v-if="validationError"
        v-bind:error="validationError.photo0" 
        />
        <Error class="u-margin--error"
        v-if="validationError"
        v-bind:error="validationError.photo1" 
        />
        <Error class="u-margin--error"
        v-if="validationError"
        v-bind:error="validationError.photo2" 
        />
        <Error class="u-margin--error"
        v-if="validationError"
        v-bind:error="validationError.photo3" 
        />
    </div>
</template>


<script>
import { mapState } from 'vuex'
import Error from '../../error'
import scheduledDateForm from './PostParts/scheduledDateForm'
import scheduledTimeForm from './PostParts/scheduledTimeForm'
import scheduledFormText from './PostParts/scheduledFormText'
import { UNPROCESSABLE_ENTITY, OK} from '../../../util'
import Loading from '../../Loading'

export default {
    data(){
        return{
            error:'',
            validationError:'',
            load:false,
            message:{
                confirm:'予約ツイートを削除しますか？',
                miss:'予約ツイートの削除に失敗しました。',
                success:'予約ツイートの削除に成功しました。',
                not_input:'文字を入力してください。',
                less_than:'現在時刻から10分未満のためツイートを予約することができません。',
                unselected:'ツイッターアカウントが未選択です。',
                error:'予約ツイートの登録に失敗しました。ブラウザを更新してください。',
                ok:'予約ツイートの登録に成功しました。'
            }
        }
    },
    components:{
        scheduledDateForm,
        scheduledTimeForm,
        scheduledFormText,
        Loading,
        Error
    },
    methods:{
        /**
         * ツイート削除
         */
        async deleteScheduled(){
            this.error = ''
            this.validationError=''
            if(confirm(this.message.confirm)){
                this.load = true
                const response = await axios.post('/api/deleteScheduled',{id:this.scheduledTweetsId})
                this.load = false
                if(response.status !==OK){
                    this.error =this.message.miss
                }else{
                    this.$store.commit('modal/isModalShow');
                    this.$store.commit('message/setContent', {
                    content: this.message.success,
                    timeout: 5000
                    })
                }
            }
        },
        /**
         * ツイートを登録
         */
        async scheduled(){
            this.error = ''
            this.validationError=''
            if(this.errorFlag === true || this.dataErrorFlag === true){
                return false
            }
            if(!this.tweetText.match(/\S/g)){
                this.error = this.message.not_input
                return false
            }
            if(!this.twitterScreenName){
                this.error = this.message.unselected
                return false
            }
            await this.timediff()
            this.load = true
            const date = new Date(this.settingDate)
            var year = date.getFullYear();
            var month= date.getMonth();
            var day = date.getDate();

            const time = new Date(this.settingTime)
            var hours = time.getHours();  
            var minutes = time.getMinutes();
            var seconds = time.getSeconds();
            /**
             * this.uploadFileList[0]['filename']が定義されている場合はawsS3に保存している画像で
             * 定義されていない場合は新規の画像になります。
             */
            const formData = new FormData()
            formData.append('photo0', ( typeof this.uploadFileList[0] !== 'undefined' && this.uploadFileList[0]['filename'])? this.uploadFileList[0]['filename']:this.uploadFileList[0])
            formData.append('photo1', ( typeof this.uploadFileList[1] !== 'undefined' && this.uploadFileList[1]['filename'])? this.uploadFileList[1]['filename']:this.uploadFileList[1])
            formData.append('photo2', ( typeof this.uploadFileList[2] !== 'undefined' && this.uploadFileList[2]['filename'])? this.uploadFileList[2]['filename']:this.uploadFileList[2])
            formData.append('photo3', ( typeof this.uploadFileList[3] !== 'undefined' && this.uploadFileList[1]['filename'])? this.uploadFileList[3]['filename']:this.uploadFileList[3])
            formData.append('year', year)
            formData.append('day', day)
            formData.append('hours',hours)
            formData.append('month',month)
            formData.append('minutes', minutes)
            formData.append('seconds', seconds)
            formData.append('tweetText', this.tweetText)
            formData.append('twetter_screen_name', this.twitterScreenName)
            formData.append('scheduledTweetsId', this.$store.state.modal.scheduledTweetsId)
            const response = await axios.post('/api/saveScheduled',formData)
            this.validationError = response.data.errors
            this.load = false
            if(response.status !== OK){
                this.error = this.message.error

            }else{
                this.$store.commit('modal/isModalShow');
                this.$store.commit('message/setContent', {
                content: this.message.ok,
                timeout: 5000
            })
            }
            
        },
        timediff(){
            this.error = ''
            const date = new Date(this.settingDate)
            var year = date.getFullYear();
            var month= date.getMonth();
            var day = date.getDate();

            const time = new Date(this.settingTime)
            var hours = time.getHours();  
            var minutes = time.getMinutes();
            var seconds = time.getSeconds();

            const reservation_date = new Date(year,month,day,hours,minutes,seconds)
            var d = new Date()
            var diffTime = reservation_date.getTime() - d.getTime();
            var diffSecond = Math.floor(diffTime / (1000));
            if(diffSecond <600 && 0<diffSecond){
                this.error = this.message.less_than
                return false
            }
        }
    },
    computed:{
         ...mapState({
            settingDate: state => state.modal.settingDate,
            settingTime: state => state.modal.settingTime,
            errorFlag: state => state.modal.errorFlag,
            dataErrorFlag: state => state.modal.dataErrorFlag,
            tweetText: state => state.modal.tweetText,
            uploadFileList: state => state.modal.uploadFileList,
            scheduledTweetsId: state=>state.modal.scheduledTweetsId,
            twitterScreenName: state=>state.modal.twitterScreenName,
        }),
    },
    watch:{
        settingDate:function(){
            this.timediff()
        },
        settingTime:function(){
            this.timediff()
        }
    }
}
</script>