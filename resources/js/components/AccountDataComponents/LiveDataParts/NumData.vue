<template>
    <div class="p-data__box">
        <div class="p-data__box__title">
            <p>24時間以内のフォロー/アンフォロー</p>
        </div>
        <Loading v-if="load" />
        <div v-else class="p-data__box__main u-padding__data">
            <ul class="p-data__box__list">
                <li class="u-margin__data--min">フォロー  {{followNum}}/1000</li>
                <li class="u-margin__data--min">アンフォロー {{ unfollowNum }}/1000</li>
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
            followNum:0,
            unfollowNum:0,
            load:false
        }
    },
    components:{
        Loading
    },
    methods:{
        //24時間以内のフォロー数をDBから取得
        async followCount(){
            this.load = true
            let followCount = await axios.post('/api/getFollowCount',{
                            twitter_screen_name:this.$route.params.screen_name
                            })
            this.load = false
            if(followCount.status !== 200){
                this.$store.commit('error/setCode',followCount.status)
            }
            this.followNum = followCount.data
            },
            //24時間以内のアンフォロー数
            async unfollowCount(){
                let unfollowCount = await axios.post('/api/getUnfollowCount',{
                                twitter_screen_name:this.$route.params.screen_name
                                })
                if(unfollowCount.status !== 200){
                    this.$store.commit('error/setCode',unfollowCount.status)
                }
                this.unfollowNum = unfollowCount.data
            },
    },
    created(){
        this.followCount()
        this.unfollowCount()
    },
    watch:{
        '$route':async function(){
            this.followCount()
            this.unfollowCount()
        }
    },
}
</script>