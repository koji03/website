<template>
    <div>
        <form action="" class="c-form" @submit.prevent="reminder" v-if="isForm">
            <Loading v-if="loading" />
            <div v-else>
                <span>送信されたメールアドレス宛に<font color="red">パスワード変更ページのURL</font>が記載されたメールを送信します。</span>

                <div class="c-form__group u-margin__input">
                    <label for="email" class="c-form__label">{{ message.label.email }}</label>
                    <input id="email" v-model="reminderForm.email" type="email" name="email" :placeholder="message.placeholder.email" class="c-form__group__input">
                </div>
                <Error  
                v-bind:error="reminderErrors.email" 
                />
                <div class="c-form__group u-margin__input--top">
                    <Submit :value="message.submit.reminder" />
                </div>       
            </div>
        </form>
        <div v-if="!isForm">
            入力いただいたメールアドレス宛にパスワード変更メールを送信しました。<br>
            1時間以内に変更手続きを完了してください。<br>
            <span style="color:red">現在使用しているブラウザでURLを開いてください。</span>
        </div>
    </div>
</template>

<script>
//エラークリアをfomboxにまとめられるかを検討する。
//エラーをstoreにまとめるべきか
import { UNPROCESSABLE_ENTITY, OK } from '../../util.js'
import Error from '../error.vue'
import Loading from '../Loading.vue'
import Submit from './Submit'
import { mapState } from 'vuex'

export default {
    components:{
        Error,
        Submit,
        Loading
    },
    data(){
        return{
            reminderForm:{
                email:''
            },
            errors:{
                email:[],
            },
            reminderErrors:{},
            isForm:true,
            loading:false
        }
    },
    props:{
        clearMessage:{
            data:Boolean,
            required:false
        }
    },
    methods:{
        async reminder(){
            this.loading = true //通信中はロード画面を出す
            const response =  await axios.post('/api/reminder',this.reminderForm)
            this.loading = false

            //通信に成功した場合
            if(response.status === OK){
                //ページの切り替え
                this.isForm = false
                window.scroll(0, 0);
                return false
            }

            //バリデーションエラーが起きた場合
            if(response.status === UNPROCESSABLE_ENTITY){
                this.reminderErrors = response.data.errors
            }else{
                //その他のエラーが起きた場合(500)
                this.$store.commit('error/setCode',response.status)
                return false
            }
        },
        setError(key,value){
            this.errors[key] = [value]
            this.reminderErrors = this.errors
        },
        emailVaridation(){
            var str = this.reminderForm.email
            var regex = /[^@]+@[^@]+/;
            var result = regex.test(str);
            if(!result){
                this.setError('email',this.errorMessage.email.email)
            }else{
                this.setError('email','')
            }
        },
    },
    watch:{
        'reminderForm.email':function(){
            if(this.reminderForm.email === ''){
                return
            }
            this.emailVaridation()
        },
    },
    computed: mapState({
        errorMessage: state => state.form.errorMessage,
        message: state => state.form.message,
    }),
    created(){
        window.scrollTo(0, 0);
    }
}
</script>