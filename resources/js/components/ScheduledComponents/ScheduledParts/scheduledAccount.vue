<template>
    <div class="">
        <div>
            予約ツイートをするアカウントを選択してください。
        </div>
        <ul class="p-scheduled__account">
            <li v-for="account in accountList" :key="account.index" class="p-scheduled__account__item" @click="setAccount(account)">
                {{account}}
            </li>
        </ul>
        <div class="c-error u-margin--error--scheduled">{{error}}</div>      
    </div>
</template>
<script>
import { mapState } from 'vuex'
export default {
  data() {
        return{
           error:'',
           fileDelete:true,
           list:[],
           fileCount:4,
           currentCount:0
        }
    },
    computed:{
        ...mapState({
            accountList: state => state.twitter.accountList,
        }),
    },
    watch:{
    },
    methods: {
        setAccount(account){
            this.$store.commit('modal/setScreenName',account)
        }
    },
    created:function(){
    },
    
}
</script>