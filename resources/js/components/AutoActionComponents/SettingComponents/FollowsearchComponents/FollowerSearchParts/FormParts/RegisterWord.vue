<template>
    <div action="" class="c-setting__form">
        <input v-if="currentForm === 'or'" v-model="list.or[0]" type="text" placeholder="255文字以内" class="c-setting__form__text">
        <input v-else-if="currentForm === 'ng'" v-model="list.ng[0]" type="text" placeholder="255文字以内" class="c-setting__form__text">
        <input v-else v-model="list.normal[0]" type="text" placeholder="255文字以内" class="c-setting__form__text">
        <input type="button" value="追加" v-bind:disabled="disabledFlag" class="c-setting__form__button" @click="saveWordList">
        {{maxNum-currentNum}}
        <div class="c-error u-margin--error">{{ followerSearchError }}</div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import { OK } from '../../../../../../util'

export default {
    data(){
        return{
            list:{
                or:[],
                ng:[],
                normal:[]
            },
            Message:{
                duplicate:'ワードで重複したため登録ができません。',
                length_limit:'255文字以内にしてください。',
                failed_register:'登録に失敗しました。再度お試しください。'
            },
            maxNum:255,
            currentNum:0,
            error:'',
            type1:'normal',
            type2:'or',
            type3:'ng',
            disabledFlag :false
        }
    },
    methods:{
        //配列同士で重複する値があるか確認する
        getIsDuplicate(arr1, arr2) {
            return [...arr1, ...arr2].filter(item => arr1.includes(item) && arr2.includes(item)).length > 0
        },
        //重複があるワードにエラーを表示する
        duplicateCheck(key,type){
            switch (key){
                case this.type1:
                    if(type === this.type1){
                        type = '必須' //normalワードは必須ワードと表現するため。
                    }
                    this.$store.commit('autoActionSetting/setFollowerSearchError',`${type}`+this.Message.duplicate)
                    break;
                case this.type2:
                    if(type === this.type1){
                        type = '必須'
                    }
                    this.$store.commit('autoActionSetting/setFollowerSearchError',`${type}`+this.Message.duplicate)
                    break;
                case this.type3:
                    if(type === this.type1){
                        type = '必須'
                    }
                    this.$store.commit('autoActionSetting/setFollowerSearchError',`${type}`+this.Message.duplicate)
                    break;
            }
        },
        validation(){
            for(let key in this.list){ //keyにはor ng normal　がそれぞれ順番に入る
                if(this.list[key].length <=0){ //配列が空の場合は次の配列をしらべる 
                    continue;
                }
                if(this.list[key][0].trim() ==''){ //空白が入っている場合は削除する
                    this.list[key] = []
                    continue;
                }
                //配列同士で重複する値がある場合エラーを入れる.
                if(this.getIsDuplicate(this.list[key],this.WordList[this.type1])){
                    this.duplicateCheck(key,this.type1)
                    for(let key in this.list){
                    return false;
                }
                }else if(this.getIsDuplicate(this.list[key],this.WordList[this.type2])){
                    this.duplicateCheck(key,this.type2)
                    for(let key in this.list){
                    return false;
                }
                }else if(this.getIsDuplicate(this.list[key],this.WordList[this.type3])){
                    this.duplicateCheck(key,this.type3)
                    for(let key in this.list){
                    return false;
                }
                }
            }
            return true;
        },
        async saveWordList(){
            this.$store.commit('autoActionSetting/setFollowerSearchLoad',true)
            this.$store.commit('autoActionSetting/setFollowerSearchError','')
            if(this.list.or.length >this.maxNum || this.list.ng.length >this.maxNum || this.list.normal.length >this.maxNum){
                this.$store.commit('autoActionSetting/setFollowerSearchError',this.Message.length_limit)
                return false
            }
            //キーワードが重複しているかを確認する
            const bool= await this.validation()
            if(!bool){
                this.$store.commit('autoActionSetting/setFollowerSearchLoad',false)
                return false
            }
            const response = await axios.post('/api/saveWordList',{
                word_list: this.list,
                twitter_screen_name: this.twitter_screen_name
            })
            if(response.status !== OK){
                for(let i in this.list){
                    this.list[i] = []
                }
                this.$store.commit('autoActionSetting/setFollowerSearchLoad',false)
                this.$store.commit('autoActionSetting/setFollowerSearchError',this.Message.failed_register)
                return false
            }
            this.$store.dispatch('autoActionSetting/setWordList',this.list)
            for(let i in this.list){
                    this.list[i] = []
            }
            this.$store.commit('autoActionSetting/setFollowerSearchLoad',false)
        },
        charCount(num){
            this.currentNum = num
            if(this.currentNum >this.maxNum){
                this.$store.commit('autoActionSetting/setFollowerSearchError',this.Message.length_limit)
                this.disabledFlag = true
            }else{
                this.disabledFlag = false
            }
        },
    },
    computed:{
        ...mapState({
            currentForm: state => state.autoActionSetting.currentForm,
            twitter_screen_name: state => state.twitter.twitter_screen_name,
            WordList: state => state.autoActionSetting.WordList,
            followerSearchError: state => state.autoActionSetting.followerSearchError
        }),
    },
    watch:{
        'list.or': function(){
            if(this.list.or[0]){
                this.charCount(this.list.or[0].length)
            }else{
                this.charCount(0)
            }
        },
        'list.ng': function(){
            if(this.list.ng[0]){
                this.charCount(this.list.ng[0].length)
            }else{
                this.charCount(0)
            }
        },
        'list.normal': function(){
            if(this.list.normal[0]){
                this.charCount(this.list.normal[0].length)
            }else{
                this.charCount(0)
            }
        },
        currentForm:function(){
            if(this.currentForm === this.type1){
                this.list.or = []
                this.list.ng = []
                if(this.list.normal[0]){
                    this.charCount(this.list.normal[0].length)
                }else{
                    this.charCount(0)
                }
            }else if(this.currentForm === this.type2){
                this.list.normal = []
                this.list.ng = []
                if(this.list.or[0]){
                    this.charCount(this.list.or[0].length)
                }else{
                    this.charCount(0)
                }
            }else if(this.currentForm === this.type3){
                this.list.normal = []
                this.list.or = []
                if(this.list.ng[0]){
                    this.charCount(this.list.ng[0].length)
                }else{
                    this.charCount(0)
                }
            }
        }
    }
    
}
</script>