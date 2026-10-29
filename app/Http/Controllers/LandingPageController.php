<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    public function index()
    {
        $jumlahProyek = DB::table('projects')
            ->where('status', 'approved')
            ->count();

        $jumlahMahasiswa = DB::table('users')
            ->where('role', 'student')
            ->count();

        $jumlahTim = DB::table('team_members')
            ->distinct()
            ->count('project_id');

        $projects = DB::table('projects')
            ->join(
                'categories',
                'projects.category_id',
                '=',
                'categories.id'
            )
            ->where('projects.status', 'approved')
            ->select(
                'projects.id',
                'projects.name',
                'projects.description',
                'projects.created_at',
                'categories.name as category_name'
            )
            ->orderByDesc('projects.created_at')
            ->limit(3)
            ->get();

        $featuredProjects = DB::table('projects')
            ->join('categories', 'projects.category_id', '=', 'categories.id')
            ->leftJoin('applications', function ($join) {
                $join->on('projects.id', '=', 'applications.project_id')
                    ->whereIn('applications.status', ['pending', 'accepted']);
            })
            ->where('projects.status', 'approved')
            ->select(
                'projects.id',
                'projects.name',
                'projects.description',
                'categories.name as category_name',
                DB::raw('COUNT(applications.id) as total_applicants')
            )
            ->groupBy(
                'projects.id',
                'projects.name',
                'projects.description',
                'categories.name'
            )
            ->orderByDesc('total_applicants')
            ->orderByDesc('projects.created_at')
            ->limit(3)
            ->get();
            
        $popularCategories = DB::table('categories')
            ->leftJoin('projects', function ($join) {
                $join->on(
                    'categories.id',
                    '=',
                    'projects.category_id'
                )->where('projects.status', '=', 'approved');
            })
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(projects.id) as total_projects')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_projects')
            ->orderBy('categories.name')
            ->limit(5)
            ->get();

        return view('landingpage', compact(
            'jumlahProyek',
            'jumlahMahasiswa',
            'jumlahTim',
            'projects',
            'featuredProjects',
            'popularCategories'
        ));
    }
}