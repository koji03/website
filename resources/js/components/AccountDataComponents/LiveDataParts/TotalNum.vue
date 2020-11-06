<template>
    <div class="p-data__box">
        <div class="p-data__box__title">
            <p>{{siteTitle}}でフォロー/アンフォローした累計</p>
        </div>
        <Loading v-if="load" />
        <div v-else class="p-data__box__main u-padding__data">
            <ul class="p-data__box__list">
                <li class="u-margin__data--min">フォロー {{followNum}}</li>
                <li class="u-margin__data--min">アンフォロー {{ unfollowNum }}</li>
            </ul>
        </div>
        
    </div>
</template>
<script>
import { mapState } from 'vuex'
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
        //このサイトでフォローした今までの合計のフォロー数
        async totalFollowCount(){
            this.load = true
            let followCount = await axios.get(`/api/followCount/${this.$route.params.screen_name}`)
            this.load = false
            if(followCount.status !== 200){
                this.$store.commit('error/setCode',followCount.status)
            }
            this.followNum = followCount.data
            },
            //このサイトで今までの合計のアンフォロー数
            async totalUnfollowCount(){
                let unfollowCount = await axios.get(`/api/unfollowCount/${this.$route.params.screen_name}`)
                if(unfollowCount.status !== 200){
                    this.$store.commit('error/setCode',unfollowCount.status)
                }
                this.unfollowNum = unfollowCount.data
            },
        },
    created(){
        this.totalFollowCount()
        this.totalUnfollowCount()
    },
    watch:{
        '$route':async function(){
            this.totalFollowCount()
            this.totalUnfollowCount()
        }
    },
    computed: {
        ...mapState({
            siteTitle: state => state.twitter.siteTitle,
        }),
    },
}
</script>