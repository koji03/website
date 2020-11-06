<template>
    <div class="p-data__box">
        <div class="p-data__box__title">
            フォロワーサーチ
        </div>
        <Loading v-if="followerSearchLoad"/>
        <div v-else class="p-data__box__main u-padding__data">
            <ul class="p-data__box__list">
                <div class="p-data__text--bold p-data__text">必須ワード</div>
                <li class="u-margin__data--min" v-for="word in word.normal" :key="word.index" >{{word}}</li>
            </ul>
            <ul class="p-data__box__list"> 
                <div class="p-data__text--bold p-data__text">ORワード</div>
                <li class="u-margin__data--min" v-for="word in word.or" :key="word.index">{{word}}</li>
            </ul>
            <ul class="p-data__box__list">
                <div class="p-data__text--bold p-data__text">NGワード</div>
                <li class="u-margin__data--min" v-for="word in word.ng" :key="word.index">{{word}}</li>
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
            word:{
                normal:[],
                or:[],
                ng:[],
            },
        }
    },
    components:{
        Loading
    },
    computed:{
        ...mapState({
            followerSearchLoad: state => state.autoActionSetting.followerSearchLoad,
        }),
    },
    methods:{
        //dbから取得しdataに格納
        async getSearchWordList(){
            const response = await axios.post('/api/getSearchWordList',{
                twitter_screen_name:this.$route.params.screen_name
            });
            this.$store.commit('autoActionSetting/setFollowerSearchLoad',false)
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
                return false
            }
            this.word = {
                normal:[],
                or:[],
                ng:[],
            }
            await response.data.forEach(element => {
                for(let i in element){
                    if(element[i].length <=0){
                            continue;
                        }
                        if(i == 'or'){
                            this.word.or = element[i]
                        }
                        if(i == 'ng'){
                            this.word.ng = element[i]
                        }
                        if(i == 'normal'){
                            this.word.normal= element[i]
                        }
                    }
            });
            this.$store.commit('autoActionSetting/setFollowerSearchLoad',false)
        },
    },
    created(){
        this.getSearchWordList()
    },
    watch:{
        '$route':async function(){
            this.getSearchWordList()
        }
    }
}
</script>