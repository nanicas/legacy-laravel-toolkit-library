<?php

namespace Nanicas\LegacyLaravelToolkit\Traits;

use Nanicas\LegacyLaravelToolkit\Exceptions\ValidatorException;
use Nanicas\LegacyLaravelToolkit\Exceptions\CustomValidatorException;
use Illuminate\Http\Request;
use Throwable;
use Nanicas\LegacyLaravelToolkit\Helpers\Helper as InternalHelper;

class_alias(InternalHelper::readTemplateConfig()['helpers']['global'], __NAMESPACE__ . '\DAPTxxHelperAlias');

trait DynamicActionPort
{
    public function dynamicAction(Request $request)
    {
        $action = $request->route('action', '');
        $id = $request->route('id', 0);

        if (empty($id) && empty($action)) {
            $id = $request->input('id');
            $action = $request->input('action');
        }

        if (method_exists($this, $action)) {
            return $this->$action($request, $action, $id);
        }

        $message = '';
        $status = false;
        $data = $request->all();
        $result = null;

        if (method_exists($this, 'defineDynamicActionParams')) {
            $definedParams = $this->defineDynamicActionParams($request, $action, $id, $data);
            extract($definedParams);
        }

        try {
            $service = $this->getService();
            $service->setRequest($request);

            $result = $service->{$action}($id, $data);
            $status = (!empty($result));

            $message = (empty($result)) ? 'Ocorreu um problema durante o processamento dessa ação de "' . $action . '"' : 'Ação executada com sucesso!';

            if (!$this->getIsAPI()) {
                $message = DAPTxxHelperAlias::loadMessage($message, $status);
            }
        } catch (ValidatorException | CustomValidatorException $ex) {
            $message = $ex->getMessage();
        } catch (Throwable $th) {
            $message = $th->getMessage();

            if (!$this->getIsAPI()) {
                $message = DAPTxxHelperAlias::loadMessage($message, $status);
            }
        }

        return $this->responseDynamicAction(compact(
            'action',
            'status',
            'message',
            'data',
            'result',
            'request',
            'id'
        ), $action);
    }

    protected function responseDynamicAction(array $data, string $action)
    {
        extract($data);

        return response()->json(DAPTxxHelperAlias::createDefaultJsonToResponse(
            $status,
            [
                'result' => $result,
                'message' => $message,
            ]
        ));
    }
}
