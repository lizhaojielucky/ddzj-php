import{R as t,L as e,r as i,a as r,o as s,b as a,w as l,f as n,e as o,D as u,k as h,l as d,C as c,g as f,t as b,F as m,j as p,q as y}from"./index.5b37717f.js";import{_ as g}from"./u-badge.eadb0f30.js";import{_ as x}from"./plugin-vue_export-helper.21dcd24c.js";var S=x({name:"u-tabs",emits:["update:modelValue","input","change"],props:{value:{type:[Number,String],default:0},modelValue:{type:[Number,String],default:0},current:{type:[Number,String],default:0},isScroll:{type:Boolean,default:!0},list:{type:Array,default:()=>[]},height:{type:[String,Number],default:80},fontSize:{type:[String,Number],default:30},duration:{type:[String,Number],default:.5},activeColor:{type:String,default:"#2979ff"},inactiveColor:{type:String,default:"#303133"},barWidth:{type:[String,Number],default:40},barHeight:{type:[String,Number],default:6},gutter:{type:[String,Number],default:30},bgColor:{type:String,default:"#ffffff"},name:{type:String,default:"name"},count:{type:String,default:"count"},offset:{type:Array,default:()=>[5,20]},bold:{type:Boolean,default:!0},activeItemStyle:{type:Object,default:()=>({})},showBar:{type:Boolean,default:!0},barStyle:{type:Object,default:()=>({})},itemWidth:{type:[Number,String],default:"auto"}},data(){return{scrollLeft:0,tabQueryInfo:[],componentWidth:0,scrollBarLeft:0,parentLeft:0,id:this.$u.guid(),currentIndex:this.current,barFirstTimeMove:!0}},watch:{list(t,e){t.length!==e.length&&(this.currentIndex=0),this.$nextTick((()=>{this.init()}))},current:{immediate:!0,handler(t,e){this.$nextTick((()=>{this.currentIndex=t,this.scrollByIndex()}))}},valueCom:{immediate:!0,handler(t,e){this.$nextTick((()=>{this.currentIndex=t,this.scrollByIndex()}))}}},computed:{valueCom(){return this.modelValue},tabBarStyle(){let t={width:this.barWidth+"rpx",transform:`translate(${this.scrollBarLeft}px, -100%)`,"transition-duration":`${this.barFirstTimeMove?0:this.duration}s`,"background-color":this.activeColor,height:this.barHeight+"rpx","border-radius":this.barHeight/2+"px"};return Object.assign(t,this.barStyle),t},tabItemStyle(){return t=>{let e={height:this.height+"rpx","line-height":this.height+"rpx","font-size":this.fontSize+"rpx","transition-duration":`${this.duration}s`,padding:this.isScroll?`0 ${this.gutter}rpx`:"",flex:this.isScroll?"auto":"1",width:this.$u.addUnit(this.itemWidth)};return t==this.currentIndex&&this.bold&&(e.fontWeight="bold"),t==this.currentIndex?(e.color=this.activeColor,e=Object.assign(e,this.activeItemStyle)):e.color=this.inactiveColor,e}}},methods:{async init(){let t=await this.$uGetRect("#"+this.id);this.parentLeft=t.left,this.componentWidth=t.width,this.getTabRect()},clickTab(t){t!=this.currentIndex&&(this.$emit("change",t),this.$emit("input",t),this.$emit("update:modelValue",t))},getTabRect(){let e=t().in(this);for(let t=0;t<this.list.length;t++)e.select(`#u-tab-item-${t}`).fields({size:!0,rect:!0});e.exec(function(t){this.tabQueryInfo=t,this.scrollByIndex()}.bind(this))},scrollByIndex(){let t=this.tabQueryInfo[this.currentIndex];if(!t)return;let i=t.width,r=t.left-this.parentLeft-(this.componentWidth-i)/2;this.scrollLeft=r<0?0:r;let s=t.left+t.width/2-this.parentLeft;this.scrollBarLeft=s-e(this.barWidth)/2,1==this.barFirstTimeMove&&setTimeout((()=>{this.barFirstTimeMove=!1}),100)}},mounted(){this.init()}},[["render",function(t,e,x,S,I,v){const $=i(r("u-badge"),g),B=p,k=y;return s(),a(B,{class:"u-tabs",style:c({background:x.bgColor})},{default:l((()=>[n(" $u.getRect()对组件根节点无效，因为写了.in(this)，故这里获取内层接点尺寸 "),o(B,{id:I.id},{default:l((()=>[o(k,{"scroll-x":"",class:"u-scroll-view","scroll-left":I.scrollLeft,"scroll-with-animation":""},{default:l((()=>[o(B,{class:u(["u-scroll-box",{"u-tabs-scorll-flex":!x.isScroll}])},{default:l((()=>[(s(!0),h(m,null,d(x.list,((t,e)=>(s(),a(B,{class:"u-tab-item u-line-1",id:"u-tab-item-"+e,key:e,onClick:t=>v.clickTab(e),style:c([v.tabItemStyle(e)])},{default:l((()=>[o($,{count:t[x.count]||t.count||0,offset:x.offset,size:"mini"},null,8,["count","offset"]),f(" "+b(t[x.name]||t.name),1)])),_:2},1032,["id","onClick","style"])))),128)),x.showBar?(s(),a(B,{key:0,class:"u-tab-bar",style:c([v.tabBarStyle])},null,8,["style"])):n("v-if",!0)])),_:1},8,["class"])])),_:1},8,["scroll-left"])])),_:1},8,["id"])])),_:1},8,["style"])}],["__scopeId","data-v-ff17e798"]]);export{S as _};