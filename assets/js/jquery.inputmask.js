(function(a){if(typeof define==="function"&&define.amd){define(["jquery","inputmask"],a)}else{if(typeof exports==="object"){module.exports=a(require("jquery"),require("./inputmask"))}else{a(jQuery,window.Inputmask)}}}(function(b,a){if(b.fn.inputmask===undefined){b.fn.inputmask=function(e,d){var f,c=this[0];if(d===undefined){d={}}if(typeof e==="string"){switch(e){case"unmaskedvalue":return c&&c.inputmask?c.inputmask.unmaskedvalue():b(c).val();case"remove":return this.each(function(){if(this.inputmask){this.inputmask.remove()}});case"getemptymask":return c&&c.inputmask?c.inputmask.getemptymask():"";case"hasMaskedValue":return c&&c.inputmask?c.inputmask.hasMaskedValue():false;case"isComplete":return c&&c.inputmask?c.inputmask.isComplete():true;case"getmetadata":return c&&c.inputmask?c.inputmask.getmetadata():undefined;case"setvalue":b(c).val(d);if(c&&c.inputmask===undefined){b(c).triggerHandler("setvalue")}break;case"option":if(typeof d==="string"){if(c&&c.inputmask!==undefined){return c.inputmask.option(d)}}else{return this.each(function(){if(this.inputmask!==undefined){return this.inputmask.option(d)}})}break;default:d.alias=e;f=new a(d);return this.each(function(){f.mask(this)})}}else{if(typeof e=="object"){f=new a(e);if(e.mask===undefined&&e.alias===undefined){return this.each(function(){if(this.inputmask!==undefined){return this.inputmask.option(e)}else{f.mask(this)}})}else{return this.each(function(){f.mask(this)})}}else{if(e===undefined){return this.each(function(){f=new a(d);f.mask(this)})}}}}}return b.fn.inputmask}));