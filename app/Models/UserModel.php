<?php

namespace App\Models;

class UserModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("user")->select("id, lastname, firstname, login, emoji, pseudo, showPseudo, idRole, idFoyer, pic, bio")->get()->getResultObject();
    }

    public function getById(int $id): object|null {
        return $this->db->table("user")->select("id, lastname, firstname, login, emoji, pseudo, showPseudo, idRole, idFoyer, pic, bio")->getWhere(["id" => $id])->getRowObject();
    }

    public function getByIdAffinities(int $id): object|null {
        return $this->db->table("user")->select("id, lastname, firstname, login, emoji, pseudo, showPseudo, idRole, idFoyer, pic")->getWhere(["id" => $id])->getRowObject();
    }

    public function getByIdFoyer(int $idFoyer): array {
        return $this->db->table("user")->select("id, lastname, firstname, login, emoji, pseudo, showPseudo, idRole, idFoyer, pic, bio")->getWhere(["idFoyer" => $idFoyer])->getResultObject();
    }

    public function getByIdRole(int $idRole): array {
        return $this->db->table("user")->select("id, lastname, firstname, login, emoji, pseudo, showPseudo, idRole, idFoyer, pic, bio")->getWhere(["idRole" => $idRole])->getResultObject();
    }

    public function getByIdRef(int $idRef): array {
        return $this->db->table("user")->select("id, lastname, firstname, login, emoji, pseudo, showPseudo, idRole, idFoyer, pic, bio")->getWhere(["idRef" => $idRef])->getResultObject();
    }

    public function getByLogin(string $login): object|null {
        return $this->db->table("user")->getWhere(["login" => $login])->getRowObject();
    }

    public function getPassword(string $idUser): string|null {
        $bdd = $this->db->table("user")->select("password")->getWhere(["id" => $idUser])->getRowObject();

        return isset($bdd) ? $bdd->password : "";
    }

    /**
     * This function adds a new user to a database table with the provided information and returns the
     * ID of the newly created user.
     * 
     * @param string lastname
     * @param string firstname
     * @param string login
     * @param string password
     * @param int idRole
     * @param int idFoyer
     * 
     * @return int ID of the newly added user or -1 if an error occurred
     */
    public function add(string $lastname, string $firstname, string $login, string $password, int $idRole, int $idFoyer): int {
        $data = [
            "id"        => $this->getMax("user", "id") + 1,
            "lastname"  => $lastname,
            "firstname" => $firstname,
            "login"     => $login,
            "password"  => $password,
            "idRole"    => $idRole,
            "idFoyer"   => $idFoyer
        ];

        $this->db->table("user")->insert($data);

        if ($this->isLastQuerySuccessful()) {
            return $data["id"];
        } else {
            return -1;
        }
    }

    public function updateData(int $idUser, object $data): bool {
        $this->db->table("user")->update($data, ["id" => $idUser]);

        return $this->isLastQuerySuccessful();
    }

    public function updatePassword(int $id, string $password): bool {
        $this->db->table("user")->update(["password" => $password], ["id" => $id]);

        return $this->isLastQuerySuccessful();
    }

    public function setActive(int $id, int $value): bool {
        $this->db->table("user")->update(["active" => $value], ["id" => $id]);

        return $this->isLastQuerySuccessful();
    }

    public function getProfilPicture(int $id): string | null {
        $result = $this->db->table("user")->select("pic")->getWhere(["id" => $id])->getRow();

        return $result == null ? null : $result->pic;
    }

    public function setProfilPicture(int $id, string|null $path): bool {
        $this->db->table("user")->update(["pic" => $path], ["id" => $id]);

        return $this->isLastQuerySuccessful();
    }

    public function getAffinities(int $idUser): array | null {
        $subquery = $this->db->table("friend fr")
            ->select("1")
            ->where("fr.idUser1", $idUser)
            ->where("fr.idUser2 = u.id", null, false)
            ->orWhere("fr.idUser1 = u.id", null, false)
            ->where("fr.idUser2", $idUser)
            ->limit(1)
            ->getCompiledSelect();

        $query = $this->db->table("user u")
            ->select("u.id as idUser")
            ->join("foyer f", "u.idFoyer = f.id")
            ->where("u.id <>", $idUser)
            ->where("NOT EXISTS ($subquery)", null, false)
            ->whereNotIn("u.id", function ($builder) use ($idUser) {
                $builder->select("idBlocked")
                    ->from("blocked")
                    ->where("idUser", $idUser);
            })
            ->whereNotIn("u.id", function ($builder) use ($idUser) {
                $builder->select("idUser")
                    ->from("blocked")
                    ->where("idBlocked", $idUser);
            })
            ->where("f.id", function ($builder) use ($idUser) {
                $builder->select("idFoyer")
                    ->from("user")
                    ->where("id", $idUser);
            });

        $result = $query->get()->getResult();

        log_message("error", $this->db->getLastQuery());
        return $result;
    }

    public function getUsersByZip(string $zip): array | null {
        return $query = $this->db->table("user")
            ->select("user.*")
            ->join("foyer", "user.idFoyer = foyer.id")
            ->where("LEFT(foyer.zip, 2)", $zip)
            ->get()->getResultObject();
    }

    public function searchUsers(string $name): array {
        $name = strtolower($name);
        return $this->db->table("user")
            ->select('id, ', 'lastname', 'firstname', 'pseudo')
            ->groupStart()
            ->orLike('LOWER(firstname)', $name)
            ->orLike('LOWER(lastname)', $name)
            ->orLike('LOWER(CONCAT(firstname, " ", lastname))', $name)
            ->orLike('LOWER(CONCAT(lastname, " ", firstname))', $name)
            ->orLike('LOWER(pseudo)', $name)
            ->groupEnd()
            ->get()
            ->getResultObject();
    }

    public function isPseudoAvailable(int $idUser, string $pseudo): bool {
        $result = $this->db->table("user")->select("1")->getWhere(["pseudo" => $pseudo, "id !=" => $idUser])->getRow();

        return $result == null;
    }
}
