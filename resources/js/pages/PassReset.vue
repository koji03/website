<template>
<div>
    <section class="u-margin--top__large u-margin--bottom__large">
            <h1 class="c-title">{{ message.title.reset }}</h1>
        <div class="c-main u-margin__form">
            <form action="" class="c-form" @submit.prevent="reset" novalidate>
            <div>
                {{ message.text.reset }}
            </div>
            <Loading v-if="loading" />
                <div v-else>
                    <div class="c-form__group u-margin__input">
                        <label for="password" class="c-form__label">{{ message.label.password }}</label>
                        <input id="password" v-model="resetForm.password" type="password" name="password" 
                        :placeholder="message.placeholder.password"
                        class="c-form__group__input">
                    </div>
                    <Error  
                        v-if="resetErrors"
                        v-bind:error="resetErrors.password" 
                    />

                    <div class="c-form__group u-margin__input--top">
                        <label for="confirmation" class="c-form__label">{{ message.label.confirmation }}</label>
                        <input if="confirmation" v-model="resetForm.password_confirmation" type="password" name="password_confirmation" 
                        :placeholder="message.placeholder.password"
                        class="c-form__group__input">
                    </div>
                    <Error  
                    v-if="resetErrors"
                    v-bind:error="resetErrors.password_confirmation" 
                    />

                    <div class="c-form__group u-margin__input--top">
                        <Submit :value="message.submit.reset" />
                    </div>
                </div>
            </form>
        </div>
    
    </section>
</div>
</template>

<script>
import { mapState,mapGetters } from 'vuex'
import { UNPROCESSABLE_ENTITY, OK } from '../util.js'
import Error from '../components/error.vue';
import Loading from '../components/Loading.vue'
import Submit from '../components/FormComponents//Submit'

export default {
    data(){
        return{
            resetForm:{
                password:'',
                password_confirmation:''
            },
            errors:{
                password:[],
                password_confirmation:[],
            },
            resetErrors:{},
            loading:false,
        }
    },
    components:{
        Error,
        Submit,
        Loading
    },
    methods:{
        async reset(){
            this.loading = true
            const response = await axios.put('/api/reset',this.resetForm)
            this.loading = false
            this.resetForm.password = ''
            this.resetForm.password_confirmation = ''
            if(response.status === OK){
                //成功したらログイン画面へ遷移
                this.$store.commit('message/setContent', {
                    content: this.message.success.reset,
                    timeout: 5000
                    })
                this.$router.push('/form/login')
                return false
            }

            //エラーが合った場合
            if (response.status === UNPROCESSABLE_ENTITY) {
                this.resetErrors = response.data.errors;
                return false
            } 
            else {
                this.$store.commit('error/setCode',response.status)
                return false
            }
        },
        setError(key,value){
            this.errors[key] = [value]
            this.resetErrors = this.errors
        },
        passwordVaridation(){
        var str = this.resetForm.password;
        var str1 = this.resetForm.password_confirmation;
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
        var str2 = this.resetForm.password;
        var str1 = this.resetForm.password_confirmation;
        if(str1 !== str2){
            this.setError('password_confirmation',this.errorMessage.password.confirmation)
        }else{
            this.setError('password_confirmation','')
        }      
        }
    },
    watch:{
    'resetForm.password_confirmation':function(){
        if(this.resetForm.password_confirmation === ''){
          return
        }
        this.confirmationVaridation()
        },
    'resetForm.password':function(){
        if(this.resetForm.password === ''){
            return
        }
        this.passwordVaridation()
        },
    },
    computed:{
        ...mapState({
            errorMessage: state => state.form.errorMessage,
            message: state => state.form.message,
        }),
    },
    created(){
        window.scrollTo(0, 0);
    }
}
</script>