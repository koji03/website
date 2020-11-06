<template>
<div>
  <form action="" class="c-form" @submit.prevent="login" novalidate>
  <h1 class="c-form__title">{{ message.title.main }}</h1>
  <Loading v-if="loading" />
  <div v-else>

    <div class="c-form__group u-margin__input">
      <label for="email" class="c-form__label">{{ message.label.email }}</label>
      <input id="email" v-model="loginForm.email" type="text" name="email" :placeholder="message.placeholder.email" class="c-form__group__input">
    </div>
      <Error  
      v-if="loginErrors"
      v-bind:error="loginErrors.email" 
      />

    <div class="c-form__group u-margin__input--top">
      <label for="password" class="c-form__label">{{ message.label.password }}</label>
      <input id="password" v-model="loginForm.password" type="password" name="password" :placeholder="message.placeholder.password" class="c-form__group__input">
    </div>
      <Error  
      v-if="loginErrors"
      v-bind:error="loginErrors.password" 
      />

    <div class="c-form__group u-margin__input--top">
      <Submit :value="message.submit.login"/>
    </div>
  </div>

    </form>
    <div class="p-anchor">
      <RouterLink class="p-anchor--blue u-margin__anchor--right" to="/form/reminder">{{ message.routerLink.reset }}</RouterLink>
      <RouterLink class="p-anchor--blue u-margin__anchor--right" to="/form/register">{{ message.routerLink.register }}</RouterLink>
    </div>
    
</div>
</template>

<script>
import { mapState } from 'vuex'
import Error from '../error.vue'
import Loading from '../Loading.vue'
import Submit from './Submit'
export default {
    components: {
      Error,
      Submit,
      Loading
  },
  data(){
      return{
          loginForm:{
              email:'',
              password:'',
          },
          errors:{
            email:[],
            password:[],
          },
          loading:false,
          passwordError:'',
          emailError:'',
      }
  },
  methods:{
    async login () {
      this.loading = true //通信中はロード画面を出す
      // auth.jsのloginアクションを呼び出す
      await this.$store.dispatch('auth/login', this.loginForm)
      this.loading = false
      this.loginForm.password = ''
        if (this.apiStatus) { //auth.js
            // ログインが成功した場合 トップページに移動する 失敗した場合は遷移しない。(errorを表示させるため)
            this.$router.push('/kamitter/home')
        }
    },
    setError(key,value){
        this.errors[key] = [value]
        this.$store.commit('auth/setLoginErrorMessages', this.errors)
    },
    emailVaridation(){
      var str = this.loginForm.email;
      var regex = /[^@]+@[^@]+/;
      var result = regex.test(str);
      if(!result){
        this.setError('email',this.errorMessage.email.email)
      }else{
        this.setError('email','')
      }
    },
    passwordVaridation(){
      var str = this.loginForm.password;
      var regex = /^(?=.*?[a-z])(?=.*?\d)[a-z\d]+$/i;
      var result = regex.test(str);
      if(str.length > 32){
        this.setError('password',this.errorMessage.password.length)
      }else if(str.length <8){
        this.setError('password',this.errorMessage.password.length)
      }else if(!result){
        this.setError('password',this.errorMessage.password.alphanumeric)
      }
      else{
        this.setError('password','')
      }
      
    },
  },
  watch:{
    'loginForm.email':function(){
      if(this.loginForm.email === ""){
        return
      }
      this.emailVaridation()
    },
    'loginForm.password':function(){
      if(this.loginForm.password === ""){
        return
      }
      this.passwordVaridation()
    },
  },
  computed:{
        ...mapState({
            apiStatus: state => state.auth.apiStatus,
            errorMessage: state => state.form.errorMessage,
            loginErrors: state => state.auth.loginErrorMessages,
            message: state => state.form.message,
        }),
    },
    created(){
        window.scrollTo(0, 0);
    }
}

</script>