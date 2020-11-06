<template>
    <div class="p-data__box">
        <div class="p-data__box__title p-data__box__title--border">
            <p>実行中のアクション</p>
        </div>
        <Loading v-if="load" />
        <div v-else class="p-data__box__main u-padding__data">
            <ul class="p-data__box__list">
                <template v-if="flag.auto_restart_flag === 1">
                    <li class="u-margin__data--min c-error" >
                        エラーが起きています。<br>
                        自動で再開されるので何もする必要はありません。
                        </li>
                </template>
                <template v-else-if="flag.error_flag === 1">
                    <li class="u-margin__data--min c-error" v-if="flag.error_flag === 1">
                        エラーが起きています。<br>
                        自動機能が停止しているので自動機能の設定から再開させてください。
                        </li>
                </template>
                <template v-else>
                    <li class="u-margin__data--min" v-if="flag.follow_flag === 1">フォロー中</li>
                    <li class="u-margin__data--min" v-if="flag.follow_target_flag === 1">フォローターゲットリスト作成中</li>
                    <li class="u-margin__data--min" v-if="flag.unfollow_flag === 1">アンフォロー中</li>
                    <li class="u-margin__data--min" v-if="flag.unfollow_target_flag === 1">アンフォローターゲットリスト作成中</li>
                    <li class="u-margin__data--min" v-if="flag.like_flag === 1">いいね中</li>
                </template>
                
            </ul>
        </div>
    </div>
</template>
<script>
import Loading from '../../Loading.vue'
import { OK } from '../../../util'
export default {
    data(){
        return{
            flag:{
                follow_flag:'',
                follow_target_flag:'',
                unfollow_flag:'',
                unfollow_target_flag:'',
                like_flag:'',
                error_flag:'',
                auto_restart_flag:'',
            },
            load:false
        }
    },
    components:{
        Loading
    },
    methods:{
        //アクションフラグをDBから取得しlistに格納する。 0か1を格納する。
        async getActionFlag(){
            this.load = true
            let actionFlag = await axios.post('/api/getActionFlag',{
                            twitter_screen_name:this.$route.params.screen_name
                            })
            this.load = false
            if(actionFlag.status !== 200){
                this.$store.commit('error/setCode',actionFlag.status)
            }
            for(let key in actionFlag.data){
                this.flag[key] = actionFlag.data[key]
            }
        },
    },
    created(){
        this.getActionFlag()
    },
    watch:{
        '$route':async function(){
        this.getActionFlag()
        }
    }
}
</script>