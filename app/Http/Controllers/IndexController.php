<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    // 获取初始化数据
    public function getSystemInit(){
        $homeInfo = [
            'title' => '首页',
            'href'  => 'index/',
        ];
        $logoInfo = [
            'title' => 'KKCAKE',
            'image' =>  'layuimini/images/logo.png',
        ];
        $menuInfo = $this->getMenuList();
        $systemInit = [
            'homeInfo' => $homeInfo,
            'logoInfo' => $logoInfo,
            'menuInfo' => $menuInfo,
        ];
        return response()->json($systemInit);
    }

    // 获取菜单列表
    private function getMenuList(){
        $menuList = DB::table('system_menu')
            ->select(['id','pid','title','icon','href','target'])
            ->where('status', 1)
            ->orderBy('sort', 'asc')
            ->get();
        $menuList = $this->buildMenuChild(0, $menuList);
        return $menuList;
    }

    //递归获取子菜单
    private function buildMenuChild($pid, $menuList){
        $treeList = [];
        foreach ($menuList as $v) {
            if ($pid == $v->pid) {
                $node = (array)$v;
                $child = $this->buildMenuChild($v->id, $menuList);
                if (!empty($child)) {
                    $node['child'] = $child;
                }
                // todo 后续此处加上用户的权限判断
                $treeList[] = $node;
            }
        }
        return $treeList;
    }
}
