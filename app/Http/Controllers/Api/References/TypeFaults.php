<?php
namespace App\Http\Controllers\Api\References;

use App\Filters\Filter;
use App\Http\Requests\Reference\TypeFault as Request;
use App\Http\Controllers\ApiController;
use App\Models\Reference\TypeFault;

class TypeFaults extends ApiController
{
    public function index(Filter $filter)
    {
        switch (request('mode')) {
            case 'all':
                $typeFaults = TypeFault::filter($filter)->get();
                break;

            case 'datagrid':
                $typeFaults = TypeFault::filter($filter)->get();

                break;

            default:
                $typeFaults = TypeFault::filter($filter)->collect();
                break;
        }

        return response()->json($typeFaults);
    }

    public function store(Request $request)
    {
        $typeFault = TypeFault::create($request->all());

        return response()->json($typeFault);
    }

    public function show($id)
    {
        $typeFault = TypeFault::findOrFail($id);
        $typeFault->setAppends(['has_relationship']);

        return response()->json($typeFault);
    }

    public function update(Request $request, $id)
    {
        $typeFault = TypeFault::findOrFail($id);

        $typeFault->update($request->input());

        return response()->json($typeFault);
    }

    public function destroy($id)
    {
        $typeFault = TypeFault::findOrFail($id);
        $typeFault->delete();

        return response()->json(['success' => true]);
    }
}
