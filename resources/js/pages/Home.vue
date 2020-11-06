<template>
    <div class="u-padding p-following__image">
        <div class="p-info">
            <Loading v-if="load || errorLoad" />
            <template v-else>
                <div class="p-info__top">
                    <div @click="twitterRegister" class="p-info__top--button">
                        ツイッターアカウントを追加
                    </div>
                    <span v-if="accountList.length === 0" class="p-info__text--min">
                        ツイッターアカウントを追加してください。
                    </span>
                    <template v-else>
                        <p class="p-info__top__item">ツイッターアカウント {{accountList.length}}/10</p>
                        <div v-if="errorAccountListFlag" class="c-error u-margin--error">
                            <p>エラーが発生し自動機能が停止しているアカウントがあります。</p> 
                            <p>エラー発生時に送信されたメールを未確認の場合は確認してください。</p>
                            <p v-if="!autoActionFlag" class="info__error info__error--min">手動で再開させる場合は自動機能の設定からできます。</p>
                            <p v-if="autoActionFlag" class="info__error info__error--min">自動で再開される場合は何もする必要はないです。</p>
                        </div>
                        <span v-else class="p-info__text--min">
                            自動機能を使用したいときは自動機能の設定から各種設定をしてください。
                        </span>
                    </template>
                </div>
                <ul class="p-info__list" >
                    <li v-for="item in accountList" :key="item.index" 
                    v-bind:class="{info__error:errorAccountList[item]}" 
                        class="p-info__list__box">
                        <span @click="twitterLink(item)"
                        class="p-info__list__item p-info__list__item--width">
                        @{{ item }}
                        </span>
                        <span @click="settingLink(item)" class="p-info__list__item"
                        >
                           自動機能の設定
                        </span>
                        <span @click="accountData(item)" class="p-info__list__item">
                            データ
                        </span>
                        <span @click="delAccount(item)" class="p-info__list__item">
                            削除
                        </span>
                    </li>
                </ul>
            </template>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import { OK } from '../util'
import Loading from './../components//Loading.vue'
export default {
    data(){
        return{
            load:false,
            errorExist:false,
            confirm:{
                regist:'ツイッターアカウントを登録しますか？',
                del:'ツイッターアカウントの登録を解除しますか？',

            }

        }
    },
    components:{
        Loading
    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
            accountList: state => state.twitter.accountList,
            errorLoad: state => state.twitter.errorLoad,
            errorAccountList: state => state.error.errorAccountList,
            errorAccountListFlag: state => state.error.errorAccountListFlag,
            autoActionFlag: state => state.error.autoActionFlag,
        }),
    },
    watch:{
        '$route':function(){
            this.$store.dispatch('error/errorCheck')
        },
    },
    methods:{
        twitterLink(id){
            window.open(`https://twitter.com/${id}`)
        },
        settingLink(index){
            document.location = `/kamitter/${index}`
        },
        twitterRegister(){
            if(!confirm(this.confirm.regist)){
                return false
            }
            document.location = `/twitter`
        },
        accountData(index){
            this.$store.commit('twitter/setAccount',index)
            this.$router.push(`/kamitter/data/${index}`)
        },
         async delAccount(account){
            if(!confirm(this.confirm.del)){
                return false
            }
            this.load = true
            const response = await axios.post('/api/deleteAccountList',{
                twitter_screen_name:account
            });
            if(response.status === OK){
                this.getTwitterAccountList()
            }else{
                this.$store.commit('error/setCode',response.status)
            }
            this.load = false
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
    },
    created:function (){
        this.$store.dispatch('error/errorCheck')
    },
    
}
</script>