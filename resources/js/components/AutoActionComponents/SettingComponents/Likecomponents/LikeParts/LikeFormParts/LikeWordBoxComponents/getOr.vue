<template>
    <div>
        <p>いずれかのキーワードが含まれているツイートをいいねします。</p>
        <ul class="p-search__box" >
            <li class="p-search__list" v-if="orList">
                <template v-for="item in orList">
                    <span class="p-search__list__item" :key="item.index">
                            {{item}}
                        <span>
                            <deleteBtn 
                                v-bind:word="item"
                                v-bind:type="'or'"
                                />
                            </span>
                        </span>
                </template>
            </li>
        </ul>
    </div>

</template>
<script>
import deleteBtn from './deleteBtn'
export default {
    components:{
        deleteBtn
    },
    computed:{
        orList () {
           return this.$store.getters['autoActionSetting/getLikeOr']
      },
    }
}
</script>