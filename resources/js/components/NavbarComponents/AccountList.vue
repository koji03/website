<template>
    <div>
        <div class="p-sidenav__wrap__title" v-bind:class="{ js_sidenav__cursor: activeAccountList }">
            <i class="fab fa-twitter p-sidenav__icon" 
            @click="isActiveChange"></i>
            <span v-bind:class="{ 
                js_sidenav__active: !activeAccountList,
                js_sidenav__hide:!activeAccountList }" class="p-sidenav__text p-sidenav__text--left">
                登録アカウント
            </span>
        </div>
        <Loading v-if="load" />
        <ul v-else >
            <div v-for="account in accountList" :key="account.index" class="p-sidenav__setting"
                v-bind:class="{
                    js_hide__account__list:hide[account],
                    js_sidenav__hide:!activeAccountList}">
                <span class="p-sidenav__text p-sidenav__setting__item"
                v-bind:class="{ js_sidenav__active: !activeAccountList }" 
                @click="isShow(account)">
                @{{ account }}
                </span>
                <transition name="p-list_fade">
                    <div v-show="show[account]" class="p-sidenav__setting">
                        <span @click="settingLink(account)" class="p-sidenav__setting__item p-sidenav__text"
                        v-bind:class="{ js_sidenav__active: !activeAccountList }">
                            自動機能の設定
                        </span>
                        <span @click="accountData(account)" class="p-sidenav__setting__item p-sidenav__text"
                        v-bind:class="{ js_sidenav__active: !activeAccountList }">
                            データ
                        </span>
                        <span @click="delAccount(account)" class="p-sidenav__setting__item p-sidenav__text"
                        v-bind:class="{ js_sidenav__active: !activeAccountList }">
                            削除
                        </span>
                    </div>
             </transition>
            </div>
        </ul>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import { OK,xl } from '../../util'
import Loading from './../../components//Loading.vue'
export default {
    data(){
        return{
            load:false,
            show:{},
            hide:{},
            width: window.innerWidth,
            message:{
                delAccount:'ツイッターアカウントの登録を解除しますか？',
                success:'ツイッターアカウントの登録の解除に成功しました。',
                miss:'ツイッターアカウントの登録の解除に失敗しました。'
            }
        }
    },
    components:{
        Loading
    },
    methods:{
        //サイドバーを表示したり隠したり。
        isActiveChange(){
            if(this.activeAccountList){
                this.$store.commit('twitter/isList')
            }
        },
        //アカウントのしたのリストを表示したり隠したり。 
        //hideに隠すアカウント+falseをshowに表示させるアカウント+trueを格納する。
        isShow(account){
            if(this.show[account] === true){
                this.hide = {}
                this.show[account] = false
                for (var i = 0; i < this.accountList.length; i++) {
                    this.$set(this.hide, this.accountList[i], false);
                }
            }else{
                this.show = {}
                this.hide = {}
                this.$set(this.show, account, true);
                for (var i = 0; i < this.accountList.length; i++) {
                    if(this.accountList[i] !== account){
                        this.$set(this.hide, this.accountList[i], true);
                    }else{
                        this.$set(this.hide, this.accountList[i], false);
                    }
                }
            }
        },
        //ツイッターアカウントに飛ぶ。
        settingLink(account){
            //横幅が一定のサイズ以下の場合はアカウントリストを閉じるようにする
            if (this.width <= xl) {
                this.$store.commit('twitter/isList')
            }
            document.location = `/kamitter/${account}`
        },
        //アカウントデータ画面に遷移する。
        accountData(account){
            //横幅が一定のサイズ以下の場合はアカウントリストを閉じるようにする
            if (this.width <= xl) {
                this.$store.commit('twitter/isList')
            }
            if(this.$route.path === `/kamitter/data/${account}`){
                return false;
            }
            this.$store.commit('twitter/setAccount',account)
            this.$router.push(`/kamitter/data/${account}`)
        },
        //アカウントを削除する.
         async delAccount(account){
            if(!confirm(this.message.delAccount)){
                return false
            }
            //横幅が一定のサイズ以下の場合はアカウントリストを閉じるようにする
            if (this.width <= xl) {
                this.$store.commit('twitter/isList')
            }
            if(this.$route.params.screen_name && this.$route.params.screen_name === account){
                this.$router.push('/kamitter/home')
            }
            this.load = true
            const response = await axios.post('/api/deleteAccountList',{
                twitter_screen_name:account
            });
            if(response.status === OK){
                this.$store.commit('message/setContent', {
                    content: this.message.success,
                    timeout: 5000
                    })
                this.getTwitterAccountList()
            }else{
                this.$store.commit('message/setContent', {
                    content: this.message.miss,
                    timeout: 5000
                    })
                this.load = false
                this.$store.commit('error/setCode',response.status)
            }
        },
        async getTwitterAccountList(){
            //ロード画面を出す
            this.load = true
            const response = await axios.get('/api/getTwitterIdList');
            this.load = false
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
                return false
            }
            //アカウントリストの配列をリセット後、１つずつpushで入れる。
            this.$store.commit('twitter/resetAccountList')
            for(let item in response.data){
                this.$store.commit('twitter/setAccountList',response.data[item].twitter_screen_name)
            }
        },
        //横幅を取得する　リアルタイムで
        handleResize: function() {
            this.width = window.innerWidth;
        }
    },
    created:function (){
        this.getTwitterAccountList()
    },
    mounted: function () {
        window.addEventListener('resize', this.handleResize)
    },
    beforeDestroy: function () {
        window.removeEventListener('resize', this.handleResize)
    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
            accountList: state => state.twitter.accountList,
            activeAccountList: state => state.twitter.activeAccountList,
        }),
    },
    watch:{
        accountList:function(){
            this.hide = {}
            for (var i = 0; i < this.accountList.length; i++) {
                this.$set(this.hide, this.accountList[i], false);
            }
        }
    },
}
</script>