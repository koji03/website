<template>
    <div class="p-sidenav" v-bind:class="{
         js_sidenav__move: activeAccountList,
         }" v-if="isLogin">
        <Logo class="u-marign__logo--medium"/>
        <div class="p-sidenav__wrap">
            <HomeButton 
            :width="width" 
            :errorAccountListFlag="errorAccountListFlag"
            :activeAccountList="activeAccountList"
            />

            <ScheduledButton 
            :isModalShow="isModalShow"
            :activeAccountList="activeAccountList"
            :width="width"/>

            <RegisterTwiButton 
            :activeAccountList="activeAccountList"
            :width="width"/>
        </div>
        <AccountList/>
    </div>

</template>

<script>
import { OK,xl } from '../../util'
import Loading from './../Loading.vue'
import { mapState, mapGetters } from 'vuex'
import Logo from './../Logo'
import AccountList from './AccountList'
import HomeButton from './SideNavComponents/HomeButton'
import ScheduledButton from './SideNavComponents/ScheduledButton'
import RegisterTwiButton from './SideNavComponents/RegisterTwiButton'
export default {
    data(){
        return{
            width: window.innerWidth,
        }
    },
    components:{
      Loading,
      Logo,
      AccountList,
      HomeButton,
      ScheduledButton,
      RegisterTwiButton
    },
    methods:{
        //画面の横幅を取得する 子コンポーネントでこの値は使う
        handleResize: function() {
            this.width = window.innerWidth;

        }
    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
            activeAccountList: state => state.twitter.activeAccountList,
            accountList: state => state.twitter.accountList,
            isModalShow: state => state.modal.modalShow,
            errorAccountListFlag: state => state.error.errorAccountListFlag,
        }),
        ...mapGetters({
            isLogin: 'auth/check'
        })
    },
    mounted: function () {
        window.addEventListener('resize', this.handleResize)
    },
    beforeDestroy: function () {
        window.removeEventListener('resize', this.handleResize)
    }
    
}
</script>