import{Z as d}from"./element-plus.62ab1451.js";import{d as l,e as p,o as m,L as _,u as i,k as c}from"./@vue.3ddec1b2.js";const D=l({__name:"index",props:{start_time:{default:""},end_time:{default:""}},emits:["update:start_time","update:end_time"],setup(o,{emit:e}){const r=o,a=p({get:()=>[r.start_time,r.end_time],set:t=>{t===null?(e("update:start_time",""),e("update:end_time","")):(e("update:start_time",t[0]),e("update:end_time",t[1]))}});return(t,n)=>{const u=d;return m(),_(u,{modelValue:i(a),"onUpdate:modelValue":n[0]||(n[0]=s=>c(a)?a.value=s:null),type:"daterange","range-separator":"-",format:"YYYY-MM-DD","value-format":"YYYY-MM-DD","start-placeholder":"\u5F00\u59CB\u65F6\u95F4","end-placeholder":"\u7ED3\u675F\u65F6\u95F4",clearable:""},null,8,["modelValue"])}}});export{D as _};