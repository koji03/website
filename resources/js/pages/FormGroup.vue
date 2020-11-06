<template>
    <section class="u-margin--top__large u-margin--bottom__large">
        <h1 class="c-title">
            <span v-if="form === 'register'">
            アカウントを作成
            </span>
            <span v-if="form === 'login'">
            ログイン
            </span>
            <span v-if="form === 'reminder'">
            パスワードを忘れた方
            </span>
        </h1>
        <div class="c-main u-margin__form">
            <login-form v-if="form === 'login'" />
            <register-form  v-if="form === 'register'"/>
            <reminder-form  v-if="form === 'reminder'" />
        </div>
    </section>
</template>
<script>
import RegisterForm from '../components/FormComponents/RegisterForm'
import LoginForm from '../components/FormComponents/LoginForm.vue'
import ReminderForm from '../components/FormComponents/Reminder.vue'
export default {
    //urlから値を取り出してformを切り替える。 form/:form
    props:{
        form:{
            type:String, 
            required:true
        }
    },
    data(){
        return{
        }
    },
    components: {
        RegisterForm,
        LoginForm,
        ReminderForm
    },
    methods:{
        //ページを切り替えたときにエラーメッセージを削除 
        clearError () {
        this.$store.commit('auth/setLoginErrorMessages', null)
        this.$store.commit('auth/setRegisterErrorMessages', null)
        ReminderForm.reminderErrors=null
        },
    },
    created () {
      this.clearError()
    },
    watch:{
        form:function(){ 
            //formを切り替えた時にエラー メッセージを削除
            this.clearError()
        }
    },
}
</script>