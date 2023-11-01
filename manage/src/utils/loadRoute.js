import router from '../router';

export const deep_serarch = function (data, index, higher, reciever) {
    let children = [];
    data[index].forEach(item => {
        if (item.parent == higher.menu_id) {
            let route = {
                id: item.menu_id,
                name: item.name,
                route_name: item.route_name,
                path: '/Home/' + item.path,
                url: item.url,
                icon: item.icon
            };
            children.push(route);
            if (index + 1 < data.length) {
                deep_serarch(data, index + 1, item, route)
            }
        };
    });
    if (children.length != 0) {
        reciever['children'] = children;
    }
}

export const handle_routes = function (array) {
    let dyn_routes = [];
    if (array.length === 0) {
        return [];
    };
    array[0].forEach(item => {
        let route = {
            id: item.menu_id,
            name: item.name,
            route_name: item.route_name,
            path: '/Home' + item.path,
            url: item.type == 1 ? item.url : null,
            icon: item.icon
        };
        if (1 < array.length) {
            deep_serarch(array, 1, item, route)
        }
        dyn_routes.push(route);
    })
    return dyn_routes;
}

export const do_add = (finalRoutes, name) => {
    finalRoutes.forEach(item => {
        router.addRoute(name, {
            name: item.route_name,
            path: item.path,
            component: resolve => require([`@/view/${item.url}`], resolve),
            meta: {
                requireAuth: true,
                name: item.name,
                menu_id: item.id
            }
        });
        if (item.children) {
            do_add(item.children, 'Home');
        }
    });
}
export const add_routes = (finalRoutes) => {
    do_add(finalRoutes, 'Home');
    router.addRoute({
        path: '*',
        redirect: '/Home/Main'
    });
}

export const deep_get = function (data, index, higher, reciever, authority, menu) {
    let children = [];
    data[index].forEach(item => {
        if (item.parent == higher.menu_id) {
            let route = null;
            if (menu === null) {
                route = {
                    id: item.menu_id,
                    label: item.name,
                };
            }
            else if (menu.indexOf(item.menu_id) > -1) {
                route = {
                    id: item.menu_id,
                    label: item.name,
                };
            }
            else {
                route = {
                    id: item.menu_id,
                    label: item.name,
                    disabled: true
                };
            }
            if (index + 1 < data.length) {
                deep_get(data, index + 1, item, route, menu)
            }
            else if (menu !== null) {
                let son = null;
                if (route.disabled) {
                    son = [
                        {
                            id: item.menu_id + '.1',
                            label: '增加',
                            disabled: true
                        },
                        {
                            id: item.menu_id + '.2',
                            label: '编辑',
                            disabled: true
                        },
                        {
                            id: item.menu_id + '.4',
                            label: '删除',
                            disabled: true
                        }]
                } else {
                    son = [
                        {
                            id: item.menu_id + '.1',
                            label: '增加'
                        },
                        {
                            id: item.menu_id + '.2',
                            label: '编辑'
                        },
                        {
                            id: item.menu_id + '.4',
                            label: '删除'
                        }]
                    let power = 0;
                    authority.forEach(it => {
                        if (it.menu_id == route.id) {
                            power = it.button;
                        }
                    });
                    switch (power) {
                        case 6:
                            son[0]['disabled'] = true;
                            break;
                        case 5:
                            son[1]['disabled'] = true;
                            break;
                        case 4:
                            son[0]['disabled'] = true;
                            son[1]['disabled'] = true;
                            break;
                        case 3:
                            son[2]['disabled'] = true;
                            break;
                        case 2:
                            son[0]['disabled'] = true;
                            son[2]['disabled'] = true;
                            break;
                        case 1:
                            son[1]['disabled'] = true;
                            son[2]['disabled'] = true;
                            break;
                        default:
                            break;
                    }

                }
                route['children'] = son;
            }
            children.push(route);
        };
    });
    if (children.length != 0) {
        reciever['children'] = children;
    }
    else if (menu !== null) {
        let children = null;
        if (reciever.disabled) {
            children = [
                {
                    id: higher.menu_id + '.1',
                    label: '增加',
                    disabled: true
                },
                {
                    id: higher.menu_id + '.2',
                    label: '编辑',
                    disabled: true
                },
                {
                    id: higher.menu_id + '.4',
                    label: '删除',
                    disabled: true
                }]
        } else {
            children = [
                {
                    id: higher.menu_id + '.1',
                    label: '增加'
                },
                {
                    id: higher.menu_id + '.2',
                    label: '编辑'
                },
                {
                    id: higher.menu_id + '.4',
                    label: '删除'
                }]
        }
        reciever['children'] = children;
    }
}

export const get_menu_tree = function (array, authority, menu = null) {
    let menu_tree = [];
    if (array.length === 0) {
        return [];
    };
    array[0].forEach(item => {
        let route = null;
        if (menu === null) {
            route = {
                id: item.menu_id,
                label: item.name,
            };
        }
        else if (menu.indexOf(item.menu_id) > -1) {
            route = {
                id: item.menu_id,
                label: item.name,
            };
        }
        else {
            route = {
                id: item.menu_id,
                label: item.name,
                disabled: true
            };
        }
        if (1 < array.length) {
            deep_get(array, 1, item, route, authority, menu)
        }
        else if (menu !== null) {
            let children = null;
            if (route.disabled) {
                children = [
                    {
                        id: item.menu_id + '.1',
                        label: '增加',
                        disabled: true
                    },
                    {
                        id: item.menu_id + '.2',
                        label: '编辑',
                        disabled: true
                    },
                    {
                        id: item.menu_id + '.4',
                        label: '删除',
                        disabled: true
                    }]
            } else {
                children = [
                    {
                        id: item.menu_id + '.1',
                        label: '增加'
                    },
                    {
                        id: item.menu_id + '.2',
                        label: '编辑'
                    },
                    {
                        id: item.menu_id + '.4',
                        label: '删除'
                    }]
                let power = 0;
                authority.forEach(it => {
                    if (it.menu_id == route.id) {
                        power = it.button;
                    }
                });
                switch (power) {
                    case 6:
                        son[0]['disabled'] = true;
                        break;
                    case 5:
                        son[1]['disabled'] = true;
                        break;
                    case 4:
                        son[0]['disabled'] = true;
                        son[1]['disabled'] = true;
                        break;
                    case 3:
                        son[2]['disabled'] = true;
                        break;
                    case 2:
                        son[0]['disabled'] = true;
                        son[2]['disabled'] = true;
                        break;
                    case 1:
                        son[1]['disabled'] = true;
                        son[2]['disabled'] = true;
                        break;
                    default:
                        break;
                }
            }
            route['children'] = children;
        }
        menu_tree.push(route);
    })
    return menu_tree;
}
export const handleTreeKey = function (checkedKeys, halfCheckedKeys) {
    let finalKeys = [];
    let handleKeys = [];
    let handlehalfKeys = [];
    let rule = RegExp(/[\.]/);
    checkedKeys.forEach(item => {
        if (rule.test(item)) {
            handleKeys.push(parseFloat(item));
        }
        else {
            handlehalfKeys.push(parseFloat(item));
        }
    })
    halfCheckedKeys.forEach(item => {
        if (rule.test(item)) {
            handleKeys.push(parseFloat(item));
        }
        else {
            handlehalfKeys.push(parseFloat(item));
        }
    })
    handlehalfKeys.forEach(item1 => {
        let sum = 0.0;
        handleKeys.forEach(item2 => {
            if (0 < item2 - item1 && item2 - item1 < 1) {
                sum += parseFloat((item2 * 10 - item1 * 10) / 10.0)
            }
        });

        sum += parseFloat(item1)
        finalKeys.push(sum);
    });
    return finalKeys;
}