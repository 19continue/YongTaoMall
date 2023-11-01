export const descartes = (pre = [], suf = []) => {
    if (!pre || pre.length < 1 || !suf || suf.length < 1) {
        return false;
    }
    let result = [];
    for (let idx in pre) {
        let arr = [];
        for (let idy in suf){
            arr.push([idx,idy]);
        }
        result.push(...arr);
    }
    // pre.forEach(o1 => {
    //     suf.forEach(o2 => {
    //         result.push([o1, o2]);
    //     })
    // })
    return result;
};
export const resDescartes = (pre = [], suf = []) => {
    if (!suf || !suf.length || suf.length < 1) {
        return false;
    }
    let res = [];
    for(let idx in suf){
        let arr = [];
        pre.forEach(o2 => {
            arr.push([...o2, idx]);
        })
        res.push(...arr);
    }
    // suf.forEach(o1 => {
    //     let arr = [];
    //     pre.forEach(o2 => {
    //         arr.push([...o2, o1]);
    //     })
    //     res.push(...arr);
    // })
    return res;
};