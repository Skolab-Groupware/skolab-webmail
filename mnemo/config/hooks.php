<?php

class Mnemo_Hooks
{
   public function prefs_init($pref, $value, $username, $scope_ob)
   {
       switch ($pref) {
       case 'default_notepad':
           $notepads = $GLOBALS['injector']->getInstance('Horde_Core_Factory_Share')
               ->create()
               ->listShares(
                   $GLOBALS['registry']->getAuth(),
                   array('perm' => Horde_Perms::SHOW,
                         'attributes' => $GLOBALS['registry']->getAuth()));
           $primary = null;
           foreach ($notepads as $id => $notepad) {
               $default = $notepad->get('default');
               if (!empty($default)) {
                   if (!empty($primary)) {
                       $GLOBALS['notification']->push(
                           sprintf(
                               "Both shares '%s' and '%s' are marked as default notepad! Please notify your administrator.",
                               $primary->get('name'),
                               $notepad->get('name')
                           ),
                           'horde.error'
                       );
                   }
                   $primary = $notepad;
               }
           }
           return $id;
       }
   }

   public function prefs_change($pref)
   {
       switch ($pref) {
       case 'default_notepad':
           $value = $GLOBALS['prefs']->getValue('default_notepad');
           $notepads = $GLOBALS['injector']->getInstance('Horde_Core_Factory_Share')
               ->create()
               ->listShares(
                   $GLOBALS['registry']->getAuth(),
                   array('perm' => Horde_Perms::SHOW,
                         'attributes' => $GLOBALS['registry']->getAuth()));
           foreach ($notepads as $id => $notepad) {
               if ($id == $value) {
                   $notepad->set('default', true);
                   $notepad->save();
                   break;
               }
           }
           break;
       }
   }
}