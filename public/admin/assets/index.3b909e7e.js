import{u as B,Q as D,_ as v,S as x,G as T,T as V}from"./element-plus.62ab1451.js";import{a as A}from"./vue-router.70b81833.js";import{g as P,h as L}from"./system.35501bbd.js";import{_ as N}from"./index.vue_vue_type_script_setup_true_lang.ec3e131a.js";import{P as C}from"./enum.41ca2dbe.js";import{P as $}from"./index.7a0b9c3a.js";import{u as q}from"./usePaging.0e69bc9a.js";import{d as M,o as r,c as R,V as t,M as o,T as i,O as S,u as m,L as n,S as c,a as h,k as j}from"./@vue.3ddec1b2.js";import"./@vueuse.b3730649.js";import"./lodash-es.32bc9704.js";import"./async-validator.fb49d0f5.js";import"./@element-plus.4237e19d.js";import"./dayjs.3f20994d.js";import"./axios.25713f9d.js";import"./@ctrl.82a509e0.js";import"./@popperjs.36402333.js";import"./escape-html.e5dfadb9.js";import"./normalize-wheel-es.8aeb3683.js";import"./index.7e7d93d0.js";import"./lodash.09c27007.js";import"./pinia.4a4088b7.js";import"./css-color-function.6cac4cf2.js";import"./color.aa9d0e7b.js";import"./clone.467d5f2b.js";import"./color-convert.755d189f.js";import"./color-name.e7a4e1d3.js";import"./color-string.e356f5de.js";import"./balanced-match.d2a36341.js";import"./ms.564e106c.js";import"./nprogress.73f6d097.js";import"./vue-clipboard3.8191fcdc.js";import"./clipboard.e51d27f9.js";import"./echarts.db1d6bb4.js";import"./zrender.84752e5a.js";import"./tslib.60310f1a.js";import"./highlight.js.4ebdf9a4.js";import"./@highlightjs.d7b1dc3a.js";const G={class:"flex"},I={class:"flex justify-end mr-4"},Tt=M({__name:"index",setup(O){const _=A(),{pager:u,getLists:p,resetPage:Q,resetParams:U}=q({fetchFun:L,params:{}}),F=async s=>{await P({id:s}),p()},g=()=>{_.push({path:"/setting/system/task/edit",query:{mode:C.ADD}})},b=s=>{_.push({path:"/setting/system/task/edit",query:{id:s,mode:C.EDIT}})};return p(),(s,f)=>{const l=B,e=D,d=v,y=x,E=T,k=V;return r(),R("div",null,[t(E,{shadow:"never"},{default:o(()=>[t(l,{type:"primary",onClick:g,class:"mb-[16px]"},{default:o(()=>[i("+\u6DFB\u52A0")]),_:1}),S((r(),n(y,{ref:"paneTable",class:"m-t-24",data:m(u).lists,style:{width:"100%"}},{default:o(()=>[t(e,{prop:"name",label:"\u540D\u79F0","min-width":"200"}),t(e,{prop:"type_desc",label:"\u7C7B\u578B","min-width":"100"}),t(e,{prop:"command",label:"\u547D\u4EE4","min-width":"160"}),t(e,{prop:"params",label:"\u53C2\u6570","min-width":"100"}),t(e,{prop:"expression",label:"\u89C4\u5219","min-width":"100"}),t(e,{prop:"status",label:"\u72B6\u6001","min-width":"80"},{default:o(({row:a})=>[a.status==1?(r(),n(d,{key:0,type:"success"},{default:o(()=>[i("\u8FD0\u884C\u4E2D")]),_:1})):c("",!0),a.status==2?(r(),n(d,{key:1,type:"info"},{default:o(()=>[i("\u5DF2\u505C\u6B62")]),_:1})):c("",!0),a.status==3?(r(),n(d,{key:2,type:"danger"},{default:o(()=>[i("\u9519\u8BEF")]),_:1})):c("",!0)]),_:1}),t(e,{prop:"error",label:"\u9519\u8BEF\u539F\u56E0","min-width":"150"}),t(e,{prop:"last_time",label:"\u6700\u540E\u6267\u884C\u65F6\u95F4","min-width":"150"}),t(e,{prop:"time",label:"\u65F6\u957F","min-width":"100"}),t(e,{prop:"max_time",label:"\u6700\u5927\u65F6\u957F","min-width":"100"}),t(e,{label:"\u64CD\u4F5C","min-width":"120"},{default:o(a=>[h("div",G,[t(l,{type:"primary",link:"",onClick:w=>b(a.row.id),class:"mr-4"},{default:o(()=>[i("\u7F16\u8F91")]),_:2},1032,["onClick"]),t($,{class:"m-l-10 m-t-20 m-b-20 inline",content:"\u786E\u5B9A\u8981\u505C\u5220\u9664\u4E2A\u5B9A\u65F6\u4EFB\u52A1\u5417\uFF1F\u8BF7\u8C28\u614E\u64CD\u4F5C",onConfirm:w=>F(a.row.id)},{trigger:o(()=>[t(l,{type:"primary",link:"",slot:"trigger"},{default:o(()=>[i("\u5220\u9664")]),_:1})]),_:2},1032,["onConfirm"])])]),_:1})]),_:1},8,["data"])),[[k,m(u).loading]]),h("div",I,[t(N,{modelValue:m(u),"onUpdate:modelValue":f[0]||(f[0]=a=>j(u)?u.value=a:null),onChange:m(p)},null,8,["modelValue","onChange"])])]),_:1})])}}});export{Tt as default};