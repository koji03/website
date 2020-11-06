<template>
  <div  class="wrapper">
    <header>
      <Message/>
      <Navbar />
    </header>
    <main class="l-main">
      <div class="container">
        <RouterView/>
      </div>
    </main>
    <Footer />
  </div>
</template>

<script>
import Navbar from './components/NavbarComponents/Navbar'
import Footer from './components/Footer.vue'
import { mapState } from 'vuex'
import Message from './components/Message.vue'

import { NOT_FOUND, UNAUTHORIZED, INTERNAL_SERVER_ERROR,User_has_been_suspended,Rate_limit_exceeded,account_is_temporarily_locked,Key_Account,User_not_found } from './util'
export default {
  components: {
    Navbar,
    Footer,
    Message,
  },
  data(){
    return{
      Rate_limit_exceeded_timer_id:null,
    }
  },
  computed: {
    errorCode () {
      return this.$store.state.error.code
    },
    ...mapState({
            siteTitle: state => state.twitter.siteTitle,
        }),
  },
  watch: {
    errorCode: {
    async handler (val) {
      if(this.Rate_limit_exceeded_timer_id){
        clearInterval(this.Rate_limit_exceeded_timer_id)
      }
      /**
       * 500エラー
       */
      if (val === INTERNAL_SERVER_ERROR) {
        this.$router.push('/500')
      /**
       * 419エラー
       */
      } else if (val === UNAUTHORIZED) {
        await axios.get('/api/refresh-token')
        this.$store.commit('auth/setUser', null)
        this.$store.commit('message/setContent', {
                    content: 'エラーが発生しました。ブラウザを更新してください。',
                    timeout: 5000
                    })
        this.$router.push('/form/login')
      /**
       * 404エラー
       */
      } else if (val === NOT_FOUND) {
        document.location = `/found`
      }
    },
    immediate: true
    },
    '$route' (to, from) {
          this.createPageTitle(to);
      }
  },
  mounted : function(){
      var to = this.$route;
      this.createPageTitle(to);
  },
  methods:{
    createPageTitle : function(to){
		 // タイトルを設定
		if(to.meta.title){
			var setTitle = to.meta.title + ' | ' + this.siteTitle;
			document.title = setTitle;
		} else {
			document.title = this.siteTitle
		}

		// メタタグdescription設定
		if(to.meta.desc){
			var setDesc = to.meta.desc + ' | ' + this.siteTitle;
			document.querySelector("meta[name='description']").setAttribute('content', setDesc)
		} else {
			document.querySelector("meta[name='description']").setAttribute('content', this.siteTitle)
		}
  	} 
  }, 
  
}
</script>