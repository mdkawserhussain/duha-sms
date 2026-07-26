<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log the activity for certain methods
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $this->getAction($request),
                'subject_type' => $this->getSubjectType($request),
                'subject_id' => $request->route('id'),
                'properties' => [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'status_code' => $response->getStatusCode(),
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }

    /**
     * Get the action based on the HTTP method.
     */
    protected function getAction(Request $request): string
    {
        return match ($request->method()) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'viewed',
        };
    }

    /**
     * Get the subject type from the route.
     */
    protected function getSubjectType(Request $request): ?string
    {
        $route = $request->route();

        if (!$route) {
            return null;
        }

        // Try to extract subject type from route name or parameters
        $routeName = $route->getName();

        if ($routeName) {
            // Map route names to model types
            $mappings = [
                'admin.students' => 'App\Models\Student',
                'admin.teachers' => 'App\Models\User',
                'admin.guardians' => 'App\Models\User',
                'admin.classes' => 'App\Models\ClassModel',
                'admin.attendance' => 'App\Models\StudentAttendance',
                'admin.evaluations' => 'App\Models\Evaluation',
                'admin.announcements' => 'App\Models\Announcement',
                'admin.events' => 'App\Models\Event',
                'admin.messages' => 'App\Models\Message',
            ];

            foreach ($mappings as $prefix => $model) {
                if (str_starts_with($routeName, $prefix)) {
                    return $model;
                }
            }
        }

        return null;
    }
}
