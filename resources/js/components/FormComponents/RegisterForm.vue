<template>
    <div>
      <form action="" class="c-form" @submit.prevent="register" novalidate>
          <h1 class="c-form__title">{{ message.title.main }}</h1>
          <span class="c-form__text">{{ message.text.register }}</span>
          <Loading v-if="loading" />
          <div v-else>
          <div class="c-form__group u-margin__input">
            <label for="name" class="c-form__label">{{ message.label.name }}</label>
            <input id="name" v-model="registerForm.name" type="text" name="name" :placeholder="message.placeholder.name" class="c-form__group__input">
          </div>
          <Error  
          v-if="registerErrors"
          v-bind:error="registerErrors.name" 
          />

          <div class="c-form__group u-margin__input--top">
            <label for="email" class="c-form__label">{{ message.label.email }}</label>
            <input id="email" v-model="registerForm.email" type="email" name="email" :placeholder="message.placeholder.email" class="c-form__group__input">
          </div>
          <Error  
            v-if="registerErrors"
            v-bind:error="registerErrors.email" 
          />

          <div class="c-form__group u-margin__input--top">
            <label for="password" class="c-form__label">{{ message.label.password }}</label>
            <input id="password" v-model="registerForm.password" type="password" name="password" :placeholder="message.placeholder.password" class="c-form__group__input">
          </div>
          <Error
            v-if="registerErrors"
            v-bind:error="registerErrors.password" 
          />

          <div class="c-form__group u-margin__input--top">
            <label for="confirmation" class="c-form__label">{{ message.label.confirmation }}</label>
            <input if="confirmation" v-model="registerForm.password_confirmation" type="password" name="password_confirmation" :placeholder="message.placeholder.password" class="c-form__group__input">
          </div>
          <Error
            v-if="registerErrors"
            v-bind:error="registerErrors.password_confirmation" 
          />
          <div class="c-form__group u-margin__input--top">
              <Submit :value="message.submit.register" />
          </div>
          </div>

      </form>
  </div>

</template>


<script>
import { mapState } from 'vuex'
import Error from '../error.vue'
import Loading from '../Loading.vue'
import Submit from './Submit'

export default {
    data(){
        return {
          registerForm:{
              name:'',
              email:'',
              password:'',
              password_confirmation:''
          },
          errors:{
            name:[],
            email:[],
            password:[],
            password_confirmation:[],
          },
          loading:false
        }
    },
    components:{
      Error,
      Submit,
      Loading
    },
  methods: {
    async register () {
      this.loading = true //ロード画面をだす
      // auth.jsのresigterアクションを呼び出す
      await this.$store.dispatch('auth/register', this.registerForm)
      this.loading = false
      this.passReset()
      if (this.apiStatus) { //通信が成功した場合
        // トップページに移動する
        this.$store.commit('message/setContent', {
                    content: this.message.success.register,
                    timeout: 5000
                    })
        this.$router.push('/kamitter/home')
      }
    },
    setError(key,value){
        this.errors[key] = [value]
        this.$store.commit('auth/setRegisterErrorMessages', this.errors)
    },
    nameVaridation(){
      var str = this.registerForm.name;
      if(str.length > 255){
        this.setError('name',this.errorMessage.name.length)
      }else{
        this.setError('name','')
      }
    },
    passwordVaridation(){
      var str = this.registerForm.password;
      var str1 = this.registerForm.password_confirmation;
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
        this.errors = {password:['']}
      }
      if(str !== str1){
        this.setError('password_confirmation',this.errorMessage.password.confirmation)
      }else{
        this.setError('password_confirmation','')
      }
    },
    confirmationVaridation(){
      var str2 = this.registerForm.password;
      var str1 = this.registerForm.password_confirmation;
      if(str1 !== str2){
        this.setError('password_confirmation',this.errorMessage.password.confirmation)
      }else{
        this.setError('password_confirmation','')
      }
    },
    emailVaridation(){
      var str = this.registerForm.email;
      var regex = /[^@]+@[^@]+/;
      var result = regex.test(str);
      if(!result){
        this.setError('email',this.errorMessage.email.email)
      }else{
        this.setError('email','')
      }
    },
    passReset(){
      this.registerForm.password = ''
      this.registerForm.password_confirmation = ''
    }
  },
  watch:{
    'registerForm.password_confirmation':function(){
      if(this.registerForm.password_confirmation === ''){
          return
      }
      this.confirmationVaridation()
    },
    'registerForm.email':function(){
      if(this.registerForm.email === ''){
          return
      }
      this.emailVaridation()
    },
    'registerForm.name':function(){
      if(this.registerForm.name === ''){
          return
      }
      this.nameVaridation()
    },
    'registerForm.password':function(){
      if(this.registerForm.password === ''){
          return
      }
      this.passwordVaridation()
    },
  },
  computed: mapState({
    apiStatus: state => state.auth.apiStatus,
    registerErrors: state => state.auth.registerErrorMessages,
    errorMessage: state => state.form.errorMessage,
    message: state => state.form.message,
  }),
  created(){
        window.scrollTo(0, 0);
    }
}
</script>